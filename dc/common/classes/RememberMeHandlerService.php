<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 22.01.2015
 * Time: 05:42
 */

class RememberMeHandlerService {

    protected $cookieSID;
    protected $cookieToken;

    /**
     * RememberMeHandlerService constructor.
     * @param VisitorRepository $repository
     * @param $siteCode
     */
    public function __construct(VisitorRepository $repository, $siteCode ) {

    }

}