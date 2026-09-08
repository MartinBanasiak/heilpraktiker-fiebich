<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.07.2015
 * Time: 10:45
 */
interface PlainRepositoryListView extends RepositoryListView
{

    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @param bool $cleanup
     * @return mixed
     */
    public function render(array $criteria, $offset = 0, $limit = 0, $cleanup = true);

    /**
     * @param $filename
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @param bool $cleanup
     * @return mixed
     */
    public function renderToFilePath($filename, array $criteria, $offset = 0, $limit = 0, $cleanup = true);


}