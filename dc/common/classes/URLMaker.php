<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/5/2015
 * Time: 2:31 PM
 */

class URLMaker {

    const PROTOCOL_HTTP = 'http';
    const PROTOCOL_HTTPS = 'https';
    const PROTOCOL_FTP = 'ftp';

    protected $protocol;
    protected $user;
    protected $password;
    protected $domain;
    protected $port;
    protected $path;
    protected $queryParams;
    protected $anchor;

    /**
     * @param $protocol
     */
    public function setProtocol( $protocol ) {
        $allowedProtocols = array(
            self::PROTOCOL_HTTP,
            self::PROTOCOL_HTTPS,
            self::PROTOCOL_FTP
        );
        if(!in_array($protocol,$allowedProtocols,true)) {
            throw new \InvalidArgumentException('Protocol \'' . $protocol . '\' is not supported');
        }
        $this->protocol = $protocol;
    }

    /**
     * @param $user
     */
    public function setUser( $user ) {
        $this->user = urlencode($user);
    }

    /**
     * @param $pass
     */
    public function setPassword( $pass ) {
        $this->password = urlencode($pass);
    }

    /**
     * @param $domain
     */
    public function setDomain( $domain ) {
        if(!$this->_isValidDomainName($domain)) {
            throw new \InvalidArgumentException('Domain-Name \'' . $domain . '\' is not valid.');
        }
        $this->domain = $domain;
    }

    /**
     * @param $port
     */
    public function setPort( $port ) {
        if(!preg_match('/^[[:digit:]]+$/',$port)) {
            throw new \InvalidArgumentException('Port is not numeric');
        }
        $this->port = (int)$port;
    }

    /**
     * @param $path
     */
    public function setPath( $path ) {
        if(!preg_match('|^\/[\/\.a-zA-Z0-9\-\+\_%]+$|',$path)) {
            throw new \InvalidArgumentException('Path is not valid. Must start with slash and contain only [a-zA-Z0-9-+_%/].');
        }
        $this->path = $path;
    }

    /**
     * @param $name
     * @param $nonURLEncodedValue
     * @internal param $value
     */
    public function addQueryParam( $name, $nonURLEncodedValue ) {
        if(!(urlencode($name) === $name)) {
            throw new \InvalidArgumentException('Parameter-Name \'' . $name . '\' is not valid in URLs');
        }
        $this->queryParams[] = array('paramName' => $name, 'paramValue' => urlencode($nonURLEncodedValue));
    }

    /**
     * @param $name
     */
    public function addAnchor( $name ) {
        if(!preg_match('/^[a-zA-Z0-9_\-]+$/',$name)) {
            throw new \InvalidArgumentException('Anchor-string is not valid. Must contain only [a-zA-Z0-9_\-].');
        }
        $this->anchor = $name;
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
     * @param $domainName
     * @return bool
     */
    protected function _isValidDomainName( $domainName ) {
        return (preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domainName) //valid chars check
            && preg_match('/^.{1,253}$/', $domainName) //overall length check
            && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domainName)   ); //length of each label
    }

    public function reset() {
        $this->anchor = null;
        $this->queryParams = null;
        $this->path = null;
        $this->port = null;
        $this->domain = null;
        $this->user = null;
        $this->password = null;
        $this->protocol = null;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getQueryStringFromParamArray(array $params ) {
        $queryString = '';
        $i = 0;
        foreach ($params as $paramKey => $paramVal) {
            if ($i > 0) {
                $queryString .= '&';
            }
            $queryString .= $paramKey . '=' . urlencode($paramVal);
            ++$i;
        }
        return $queryString;
    }

}