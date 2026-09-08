<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 12:09 PM
 */
final class URL
{


    const PROTOCOL_HTTP = 'http';
    const PROTOCOL_HTTPS = 'https';
    const PROTOCOL_FTP = 'ftp';

    private $protocol;
    private $user;
    private $password;
    private $domain;
    private $port;
    private $path;
    private $queryParams;
    private $anchor;

    /**
     * URL constructor.
     * @param $protocol
     * @param null $user
     * @param null $password
     * @param $domain
     * @param null $port
     * @param null $path
     * @param array|null $queryParams
     * @param null $anchor
     */
    public function __construct($protocol, $user = null, $password = null, $domain, $port = null, $path = null, array $queryParams = null, $anchor = null)
    {
        if (null === $protocol || !$this->isProtocolValid($protocol)) {
            throw new \InvalidArgumentException(
                'Protocol `' . $protocol . '`is not a valid protocol. Only http, https & ftp are allowed'
            );
        }

        if (null === $domain || !$this->isDomainValid($domain)) {
            throw new \InvalidArgumentException(
                'Domain is not valid.'
            );
        }

        if (null !== $port && !$this->isPortValid($port)) {
            throw new \InvalidArgumentException(
                'Port is not valid. Valid range is 0-65535'
            );
        }

        if (null !== $path && !$this->isPathValid($path)) {
            throw new \InvalidArgumentException(
                'Path is not a valid URL-path'
            );
        }

        if (null !== $queryParams) {
            foreach ($queryParams as $paramKey => $paramValue) {
                if (!$this->isQueryParamValid([$paramKey => $paramValue])) {
                    throw new \InvalidArgumentException(
                        'Query parameter name must not differ from its URL-encoding. Paramter `' . htmlentities($paramKey) . '` does not meet this criterion.'
                    );
                }
            }
        }

        if (null !== $anchor && !$this->isAnchorValid($anchor)) {
            throw new \InvalidArgumentException(
                'Ancher is not valid.'
            );
        }

        $this->protocol = $protocol;
        $this->user = $user;
        $this->password = $password;
        $this->domain = $domain;
        $this->port = $port;
        $this->path = $path;
        $this->queryParams = $queryParams;
        $this->anchor = $anchor;
    }

    /**
     * @param $protocol
     * @return URL
     */
    public function withProtocol($protocol)
    {
        return new self($protocol, $this->user, $this->password, $this->domain, $this->port, $this->path, $this->queryParams, $this->anchor);
    }

    /**
     * @param $user
     * @return URL
     */
    public function withUser($user)
    {
        return new self($this->protocol, $user, $this->password, $this->domain, $this->port, $this->path, $this->queryParams, $this->anchor);
    }

    /**
     * @param $password
     * @return URL
     */
    public function withPassword($password)
    {
        return new self($this->protocol, $this->user, $password, $this->domain, $this->port, $this->path, $this->queryParams, $this->anchor);
    }

    /**
     * @param $user
     * @param $password
     * @return URL
     */
    public function withUserPassword($user, $password)
    {
        return new self($this->protocol, $user, $password, $this->domain, $this->port, $this->path, $this->queryParams, $this->anchor);
    }

    /**
     * @param $domain
     * @return URL
     */
    public function withDomain($domain)
    {
        return new self($this->protocol, $this->user, $this->password, $domain, $this->port, $this->path, $this->queryParams, $this->anchor);
    }

    /**
     * @param $port
     * @return URL
     */
    public function withPort($port)
    {
        return new self($this->protocol, $this->user, $this->password, $this->domain, $port, $this->path, $this->queryParams, $this->anchor);
    }

    /**
     * @param $path
     * @return URL
     */
    public function withPath($path)
    {
        return new self($this->protocol, $this->user, $this->password, $this->domain, $this->port, $path, $this->queryParams, $this->anchor);
    }

    /**
     * @param array $params
     * @return static
     */
    public function withAddedQueryParams(array $params)
    {
        $newParamArray = array_merge($this->queryParams, $params);
        return new static($this->protocol, $this->user, $this->password, $this->domain, $this->port, $this->path, $newParamArray, $this->anchor);
    }

    /**
     * @param array $params
     * @return static
     */
    public function withNewQueryParams(array $params)
    {
        return new static($this->protocol, $this->user, $this->password, $this->domain, $this->port, $this->path, $params, $this->anchor);
    }

    /**
     * @param $anchor
     * @return URL
     */
    public function withAnchor($anchor)
    {
        return new self($this->protocol, $this->user, $this->password, $this->domain, $this->port, $this->path, $this->queryParams, $anchor);
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        $queryString = '';
        if (is_array($this->queryParams) && count($this->queryParams) > 0) {
            $queryString .= '?';
            $i = 0;
            foreach ($this->queryParams as $paramName => $paramVal) {
                if ($i > 0) {
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
    public function getFullURL()
    {
        if (null === $this->domain) {
            throw new \RuntimeException('No domain-name set.');
        }
        if (null === $this->protocol) {
            throw new \RuntimeException('No protocol set.');
        }

        //Protocol
        $url = $this->protocol . '://';
        //User/Pass
        if (null !== $this->user) {
            $url .= rawurlencode($this->user) . ':';
            if (null !== $this->password) {
                $url .= rawurlencode($this->password);
            }
            $url .= '@';
        }
        //Domain
        $url .= $this->domain;
        //Port
        if (null !== $this->port) {
            $url .= ':' . $this->port;
        }
        //Path
        if (null !== $this->path) {
            $url .= $this->path;
        }
        //QueryString
        if (null !== $this->queryParams) {
            $url .= $this->getQueryString();
        }
        //Anchor
        if (null !== $this->anchor) {
            $url .= '#' . $this->anchor;
        }
        return $url;
    }


    /**
     * @param $protocol
     * @return bool
     */
    protected function isProtocolValid($protocol)
    {
        return in_array($protocol, [
            self::PROTOCOL_HTTP,
            self::PROTOCOL_HTTPS,
            self::PROTOCOL_FTP
        ], true);
    }

    /**
     * @param $domain
     * @return bool
     */
    private function isDomainValid($domain)
    {
        return (preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domain) //valid chars check
            && preg_match('/^.{1,253}$/', $domain) //overall length check
            && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domain)); //length of each label
    }

    /**
     * @param $port
     * @return bool
     */
    private function isPortValid($port)
    {
        return ((is_int($port) || ctype_digit($port)) && $port >= 0 && $port <= 65535);
    }

    /**
     * @param $path
     * @return int
     */
    private function isPathValid($path)
    {
        return preg_match('|^\/[a-zA-Z0-9\-\+\_%]+(?:[\/\a-zA-Z0-9\-\+\_%]+)*$|', $path);
    }

    /**
     * @param array $queryParam
     * @return bool
     */
    private function isQueryParamValid(array $queryParam)
    {
        $firstKey = array_keys($queryParam)[0];
        $firstVal = array_values($queryParam)[0];
        return (count($queryParam) === 1 && $firstKey === urlencode($firstKey) && is_scalar($firstVal));
    }

    /**
     * @param $anchor
     * @return int
     */
    private function isAnchorValid($anchor)
    {
        return preg_match('/^[a-zA-Z0-9_\-]+$/', $anchor);
    }


    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @return null
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * @param $domain
     * @return URL
     */
    public static function createHTTPFromDomain($domain)
    {
        return new self(self::PROTOCOL_HTTP,null,null,$domain,null,null,null,null);
    }

    /**
     * @param $domain
     * @return URL
     */
    public static function createHTTPSFromDomain($domain)
    {
        return new self(self::PROTOCOL_HTTPS,null,null,$domain,null,null,null,null);
    }

    /**
     * @param $domain
     * @param $path
     * @return URL
     */
    public static function createHTTPFromDomainAndPath($domain, $path)
    {
        return new self(self::PROTOCOL_HTTP,null,null,$domain,null,$path,null,null);
    }

    /**
     * @param $domain
     * @param $path
     * @return URL
     */
    public static function createHTTPSFromDomainAndPath($domain, $path)
    {
        return new self(self::PROTOCOL_HTTPS,null,null,$domain,null,$path,null,null);
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getFullURL();
    }

}
/*
$url1 = new URL(URL::PROTOCOL_HTTPS, 'bauer@dc-solution.de', '!mB11121983!', 'www.dc-solution.de', 88, '/dc/de/software/dccms/', ['test1name' => 'test1value'], 'anchor1');
$url2 = $url1->withDomain('www.dc-solution.de')
            ->withUserPassword(null,null)
            ->withPort(null)
            ->withNewQueryParams([])
            ->withAnchor(null);
$url3 = URL::createHTTPFromDomain('www.arstechnica.com');
$url4 = URL::createHTTPSFromDomainAndPath('www.arstechnica.com','/httpstest/is/here/in/this/file.php');
echo 'URL1: ' . $url1->getFullURL();
echo '
URL2: ' . $url2->getFullURL();
echo '
URL3: ' . $url3->getFullURL();
echo '
URL4: ' . $url4->getFullURL();*/