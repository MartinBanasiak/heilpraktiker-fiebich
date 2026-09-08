<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 12:05 AM
 */
interface View
{

    /**
     * @return string
     */
    public function render();

    /**
     * @param $filePath
     * @return
     */
    public function renderToFilePath($filePath);

}