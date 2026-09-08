<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 04.11.2015
 * Time: 01:04
 */
class AutoloaderBuilder
{
    const NAMESPACE_PATTERN = '/^namespace\s+([a-zA-Z0-9_\\\\\/]+);/m';
    const CLASSNAME_PATTERN = '/^(?:abstract\s+|final\s+|private\s+|public\s+|protected\s+)*class\s+([a-zA-Z0-9_\\\]+)/m';
    const TRAIT_PATTERN = '/^(?:private\s+|public\s+|protected\s+)*trait\s+([a-zA-Z0-9_\\\]+)/m';
    const INTERFACE_PATTERN = '/^interface\s+([a-zA-Z0-9_\\\]+)/m';

    private static $autoloaderTemplate =
<<<'TMPL'
<?php

class {{class_name}}
{
    private static $map = {{{mapping_array}}};

    public function register()
    {
        spl_autoload_register([__NAMESPACE__ . '\\' . __CLASS__,'resolve']);
    }

    public static function resolve($class)
    {
        if (isset(static::$map[$class])) {
            foreach (static::$map[$class] as $includePath) {
                include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . $includePath);
            }
        }
    }

}
TMPL;

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    public function __construct(\DynCom\dc\common\interfaces\TemplateEngine $engine)
    {
        $this->templateEngine = $engine;
    }

    public function build(array $directories,$targetName,$targetPath)
    {

        $output = [];
        $dirs = [];
        foreach($directories as $directoryPath) {

            if(strpos($directoryPath,rtrim($_SERVER['DOCUMENT_ROOT'],'/')) === false) {
                $directoryPath = rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/' . $directoryPath;
            }
            if (is_dir($directoryPath)) {
                $innerIterator = new RecursiveDirectoryIterator($directoryPath);
                $outerIterator = new RecursiveIteratorIterator($innerIterator);
                $regedxIterator = new RegexIterator($outerIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
                $dirs[$directoryPath] = $regedxIterator;
                for($regedxIterator->rewind();$regedxIterator->valid();$regedxIterator->next()) {
                    $cur = $regedxIterator->current();
                    $this->checkAndAddToOutput($cur[0],$output);
                }
            }
        }
        $mappingArray = var_export($output,true);
        $replacements = [
            'class_name' => $targetName,
            'mapping_array' => $mappingArray
        ];
        $outputStr = $this->templateEngine->render(self::$autoloaderTemplate,$replacements);

        try {
            file_put_contents(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/' . $targetPath . '/' . $targetName . '.php',$outputStr);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    private function checkAndAddToOutput($filepath,&$output) {
        if(is_readable($filepath)) {
            $contents = file_get_contents($filepath);
            $matches = [];
            $namespace = '';
            $namespaceExists = preg_match(self::NAMESPACE_PATTERN,$contents,$matches);
            if($namespaceExists && array_key_exists(1,$matches) && !empty($matches[1])) {
                $namespace = $matches[1];
            }
            $classnameMatches = [];
            $traitNameMatches = [];
            $interfaceNameMatches = [];
            $hasClasses = preg_match_all(self::CLASSNAME_PATTERN,$contents,$classnameMatches);
            $hasTraits = preg_match_all(self::TRAIT_PATTERN,$contents,$traitNameMatches);
            $hasInterfaces = preg_match_all(self::INTERFACE_PATTERN,$contents,$interfaceNameMatches);
            $friendlyNames = [];
            $fqns = [];
            if($hasInterfaces && is_array($interfaceNameMatches[1])) {
                foreach($interfaceNameMatches[1] as $interfaceName) {
                    $friendlyName = $interfaceName;
                    if($namespace !== '') {
                        $fqn = $namespace . '/' . $friendlyName;
                        $friendlyNames[] = $friendlyName;
                    } else {
                        $fqn = $friendlyName;
                    }
                    $fqns[] = $fqn;
                }
            }
            if($hasTraits && is_array($traitNameMatches[1])) {
                foreach($traitNameMatches[1] as $traitName) {
                    $friendlyName = $traitName;
                    if($namespace !== '') {
                        $fqn = $namespace . '/' . $friendlyName;
                        $friendlyNames[] = $friendlyName;
                    } else {
                        $fqn = $friendlyName;
                    }
                    $fqns[] = $fqn;
                }
            }
            if($hasClasses && is_array($classnameMatches[1])) {
                foreach($classnameMatches[1] as $className) {
                    $friendlyName = $className;
                    if($namespace !== '') {
                        $fqn = $namespace . '/' . $friendlyName;
                        $friendlyNames[] = $friendlyName;
                    } else {
                        $fqn = $friendlyName;
                    }
                    $fqns[] = $fqn;
                }
            }
            if(count($fqns) > 0) {
                $this->addFileResourcesToOutput($output, $filepath, $fqns, $friendlyNames);
            }
        }
    }

    private function addFileResourcesToOutput(&$outputArr, $filepath, array $fullyQualifiedNames, array $friendlyNames = [])
    {
        $filepath = str_replace(rtrim($_SERVER['DOCUMENT_ROOT'],'/'),'',$filepath);
        foreach ($fullyQualifiedNames as $fqn) {
            if (!array_key_exists($fqn,$outputArr) || !in_array($filepath,$outputArr[$fqn],true)) {
                $fqn = str_replace(['\\\\','\\'],'/',$fqn);
                $outputArr[$fqn][] = $filepath;
            }
        }
        /*
        foreach ($friendlyNames as $friendlyName) {
            if (!array_key_exists($friendlyName,$outputArr) || !in_array($filepath,$outputArr[$friendlyName],true)) {
                $outputArr[$friendlyName][] = $filepath;
            }
        }
        */
    }
}