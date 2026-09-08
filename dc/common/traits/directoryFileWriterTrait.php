<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/14/2015
 * Time: 2:51 PM
 */
trait directoryFileWriterTrait {

    protected $lowestWritableDir = '/htdocs';
    protected $enforceLowestWriteLevelLimit = true;
    protected $classOrSelfReferencePath = 'self';
    protected $canSidestep = true;

    /**
     * @return array|bool|mixed|string
     */
    protected function _getCurrentScriptPath() {
        $scriptPath = $this->truepath(dirname((new \ReflectionClass(get_class($this)))->getFileName()));
        return $scriptPath;
    }

    /**
     * @return array|bool|mixed|string
     */
    protected function _getCurrentSelfPath() {
        $scriptPath = $this->truepath(dirname($_SERVER['PHP_SELF']));
        return $scriptPath;
    }

    /**
     * @param $directoryString
     * @return bool
     */
    protected function canWriteToDirectory($directoryString) {
        if($this->classOrSelfReferencePath === 'self') {
            $curDir = $this->_getCurrentSelfPath();
        } elseif($this->classOrSelfReferencePath == 'class') {
            $curDir = $this->_getCurrentScriptPath();
        } else {
            $curDir = $this->_getCurrentSelfPath();
        }
        $curDirArr = $this->_createPathsArrFromDirStr($curDir);
        $dirArr = $this->truepath($directoryString,true,true,true);
        $dir = max($dirArr);
        if($this->enforceLowestWriteLevelLimit) {
            $realLowestWritableDir = $this->truepath($this->lowestWritableDir);
            $lowestPermittedLevel = count(explode(DIRECTORY_SEPARATOR,$realLowestWritableDir));
            $requestedLevel = count(explode(DIRECTORY_SEPARATOR,$dir));
            if($this->canSidestep) {
                return (($requestedLevel <= $lowestPermittedLevel) && is_writable($dir));
            } else {
                return (($requestedLevel <= $lowestPermittedLevel) && in_array($dir,$curDirArr) && is_writable($dir));
            }
        } elseif ($this->canSidestep) {
            return(is_writable($dir));
        } else {
            return(in_array($dir,$curDirArr) && is_writable($dir));
        }

    }

    /**
     * @param $path
     * @param bool|false $asArray
     * @param bool|false $mustExist
     * @param bool|false $mustBeWritable
     * @return array|bool|mixed|string
     */
    protected function truepath($path,$asArray = false, $mustExist = false, $mustBeWritable = false){

        // whether $path is unix or not
        $unipath=strlen($path)==0 || $path{0}!='/';
        // attempts to detect if path is relative in which case, add cwd
        if ($unipath && strpos($path,':') === false) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }
        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.'  === $part) { continue; }
            if ('..' === $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path=implode(DIRECTORY_SEPARATOR, $absolutes);
        // resolve any symlinks
        $pathExists = false;
        $pathIsWritable = false;

        if(file_exists($path)) {
            $pathExists = true;
            if(is_writable($path)) {
                $pathIsWritable = true;
            }
        }

        if ($pathExists && linkinfo($path) > 0) { $path = readlink($path); }
        // put initial separator that could have been lost
        $path = !$unipath ? '/'.$path : $path;
        if((!$mustExist || $pathExists) && (!$mustBeWritable || $pathIsWritable)) {
            if($asArray) {
                $paths = $this->_createPathsArrFromDirStr($path);
                return $paths;
            }
            return $path;
        } else {
            return false;
        }
    }

    /**
     * @param $dirStr
     * @param string $separator
     * @return array
     */
    protected function _createPathsArrFromDirStr($dirStr, $separator = DIRECTORY_SEPARATOR) {
        $arr = explode($separator,$dirStr);
        $segCount = count($arr);
        $paths = array();
        for($i = 1;$i <= $segCount;++$i) {
            $tmpArr = array_slice($arr,0,$i-1);
            $str = implode($separator,$tmpArr);
            $paths[$i-1] .= $str;
        }
        return $paths;
    }

    /**
     * @param $dir
     * @param $referenceDir
     * @return int
     */
    protected function getDirLevelComparison($dir, $referenceDir){

        $realDir = $this->truepath($dir);
        $realReferenceDir = $this->truepath($referenceDir);

        $dirLevel = count(explode(DIRECTORY_SEPARATOR, $realDir));
        $referenceDirLevel = count(explode('/', $realReferenceDir));

        return ($dirLevel - $referenceDirLevel);

    }

}