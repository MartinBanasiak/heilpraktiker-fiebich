<?php
namespace DynCom\dc\common\traits;
/**
 * Class genericViewTrait
 */
trait genericViewTrait
{
    use directoryFileWriterTrait;

    abstract public function render();

    /**
     * @param $filePath
     * @param bool $replace
     * @return bool
     */
    public function renderToFilePath($filePath, $replace = false) {
        $filepath = $this->truepath($filePath,false,true,true);
        if($filepath && $this->canWriteToDirectory($filePath)) {
            $flags = 0;
            if($replace) $flags = (FILE_APPEND | LOCK_EX);
            return (file_put_contents($filePath,$this->render()) > 0);
        }
        return false;
    }

}