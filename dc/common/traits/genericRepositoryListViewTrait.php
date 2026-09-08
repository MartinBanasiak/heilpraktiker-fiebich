<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.07.2015
 * Time: 11:22
 */
trait genericRepositoryListViewTrait
{

    use directoryFileWriterTrait;

    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    abstract public function render(array $criteria, $offset = 0, $limit = 0);

    /**
     * @param $filePath
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @param bool $replace
     * @return bool
     */
    public function renderToFilePath($filePath, array $criteria, $offset = 0, $limit = 0, $replace = false) {
        $filepath = $this->truepath($filePath,false,true,true);
        if($filepath && $this->canWriteToDirectory($filePath)) {
            $flags = 0;
            if($replace) $flags = (FILE_APPEND | LOCK_EX);
            return (file_put_contents($filePath,$this->render($criteria,$offset,$limit)) > 0);
        }
        return false;
    }

}