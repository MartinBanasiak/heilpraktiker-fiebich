<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 29.01.2015
 * Time: 11:11
 */

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 22.01.2015
 * Time: 14:22
 */
interface PageableViewInterface {

    /**
     * @return bool
     */
    public function render();

    /**
     * @return string
     */
    public function getContent();

    public function echoContent();

    /**
     * @param int $fromElementNo
     * @param int $toElementNo
     * @return mixed
     */
    public function getData($fromElementNo = 0, $toElementNo = -1);

}