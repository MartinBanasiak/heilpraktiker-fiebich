<?php
namespace DynCom\dc\common\abstracts;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 12:11 PM
 */

abstract class URLBase {

    protected $protocol;
    protected $user;
    protected $password;
    protected $domain;
    protected $port;
    protected $path;
    protected $queryParams;
    protected $anchor;

    /**
     * @return string
     */
    public function getQueryString() {
        $queryString = '';
        if(is_array($this->queryParams)) {
            $queryString .= '?';
            $i = 0;
            foreach($this->queryParams as $paramArr) {
                $paramName = $paramArr['paramName'];
                $paramVal = $paramArr['paramValue'];
                if($i > 0) {
                    $queryString .= '&';
                }
                $queryString .= $paramName . '=' . $paramVal;
                ++$i;
            }
        }
        return $queryString;
    }

    /**
     * @return string
     */
    public function getFullURL() {
        if(null === ($this->domain)) {
            throw new \RuntimeException('No domain-name set.');
        }
        if(null === ($this->protocol)) {
            throw new \RuntimeException('No protocol set.');
        }

        //Protocol
        $url = $this->protocol . '://';
        //User/Pass
        if(null !== $this->user) {
            $url .= $this->user . ':';
            if(null !== $this->password) {
                $url .= $this->password;
            }
            $url .= '@';
        }
        //Domain
        $url .= $this->domain;
        //Port
        if(null !== $this->port) {
            $url .= ':' . $this->port;
        }
        //Path
        if(null !== $this->path) {
            $url .= $this->path;
        }
        //QueryString
        if(null !== $this->queryParams) {
            $url .= $this->getQueryString();
        }
        //Anchor
        if(null !== $this->anchor) {
            $url .= '#' . $this->anchor;
        }
        return $url;
    }
}