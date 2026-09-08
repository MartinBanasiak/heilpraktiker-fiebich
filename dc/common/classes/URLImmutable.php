<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\abstracts\URLBase;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 12:09 PM
 */

class URLImmutable extends URLBase {


    /**
     * URLImmutable constructor.
     * @param $protocol
     * @param $user
     * @param $password
     * @param $domain
     * @param $port
     * @param $path
     * @param $queryParams
     * @param $anchor
     */
    public function __construct($protocol, $user, $password, $domain, $port, $path, $queryParams, $anchor) {
        $trace = debug_backtrace();
        if(!isset($trace[1]['class']) || $trace[1]['class'] != 'URLMaker') {
            throw new \DomainException('URLImmutable must only be constructed from URLMaker, not ' . $trace[1]['class']);
        }

        $this->protocol = $protocol;
        $this->user = $user;
        $this->password = $password;
        $this->domain = $domain;
        $this->path = $path;
        $this->queryParams = $queryParams;
        $this->anchor = $anchor;
    }

}