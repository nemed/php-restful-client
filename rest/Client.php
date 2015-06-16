<?php
namespace ctur\rest;

use Exception;
use ctur\xml\Formatter;

/**
 * Restful client.
 * Example:
 * (new Client(Client::POST, $url, $data))->setContentType(Client::JSON)->setUserAgent('Yah')->setHttpAuth('aloha', '123123123')->call();
 * Generate link and create request to http://myapi.com/api.php?param1=1&param2=2
 * Supported methods: POST, PUT, GET, DELETE
 *
 * @package ctur\rest
 * @author Cyril Turkevich
 */
class Client
{
    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const DELETE = 'delete';

    const JSON = 'application/json';
    const XML = 'application/xml';

    /* @var string $_contentType content type for this request. */
    protected $_contentType = self::JSON;

    /* @var resource $_request curl request resource. */
    protected $_request = null;

    /* @var string $_userAgent user agent. */
    protected $_userAgent = 'REST PHP Client/1.0';

    /* @var bool $_httpAuth if your request to api with http auth, also you must set username & password. */
    protected $_httpAuth = false;

    /* @var string $_url url to api service. */
    protected $_url = '';

    /* @var string $_username http auth username. */
    protected $_username = '';

    /* @var string $_password http auth password. */
    protected $_password = '';

    /* @var string $_method rest method use class const POST, PUT, GET, DELETE */
    protected $_method;

    /* @var array|null $data array("param" => "value") ==> index.php?param=value */
    protected $_data;

    /* @var array|null $_requestContentType curl request ContentType. */
    protected $_requestContentType;

    /* @var bool $_useRequestContentType use curl request ContentType. */
    protected $_useRequestContentType = false;

    /**
     * @param int $method use class const POST, PUT, GET, DELETE
     * @param string $url url to api
     * @param array|null $data array("param" => "value") ==> index.php?param=value
     * @param bool $useRequestContentType
     * @throws Exception
     */
    public function __construct($method, $url, array $data = null, $useRequestContentType = false)
    {
        if (!method_exists($this, $method)) {
            throw new Exception('Undefined rest method!');
        }

        $this->_method = $method;
        $this->_url = $url;
        $this->_data = $data;
        $this->_useRequestContentType = $useRequestContentType;
    }

    /**
     * Apply http authentication.
     */
    protected function httpAuth()
    {
        curl_setopt($this->_request, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->_request, CURLOPT_USERPWD, "{$this->_username}:{$this->_password}");
    }

    /**
     * Apply rest PUT method.
     */
    protected function put()
    {
        curl_setopt($this->_request, CURLOPT_PUT, 1);
    }

    /**
     * Apply rest POST method.
     */
    protected function post()
    {
        curl_setopt($this->_request, CURLOPT_POST, 1);
        $this->_data && curl_setopt($this->_request, CURLOPT_POSTFIELDS, $this->getFormattedData());
    }

    /**
     * Apply rest GET method.
     */
    protected function get()
    {
        if ($this->_data) {
            $this->_url = sprintf("%s?%s", $this->_url, http_build_query($this->_data));
        }
    }

    /**
     * Apply rest DELETE method.
     */
    protected function delete()
    {
        curl_setopt($this->_request, CURLOPT_CUSTOMREQUEST, "DELETE");
        $this->_data && curl_setopt($this->_request, CURLOPT_POSTFIELDS, $this->getFormattedData());
    }

    /**
     * @param string $string string for format.
     * @param bool $asObject if you need get result as object.
     * @return array|\stdClass
     * @throws Exception
     */
    protected function formatResult($string, $asObject)
    {
        $contentType = !empty($this->_requestContentType) ? $this->_requestContentType : $this->_contentType;
        switch ($contentType) {
            case self::JSON :
                $result = json_decode($string);
                break;
            case self::XML :
                $result = Formatter::toArray($string);
                break;
            default :
                throw new Exception('Undefined rest method!');
        }

        return $asObject ? (object)$result : $result;
    }

    /**
     * @return \SimpleXMLElement|string
     * @throws Exception
     */
    protected function getFormattedData()
    {
        switch ($this->_contentType) {
            case self::JSON :
                $result = json_encode($this->_data);
                break;
            case self::XML :
                $result = Formatter::toXml($this->_data);
                break;
            default :
                throw new Exception('Undefined rest method!');
        }

        return $result;
    }

    /**
     * @param string $value content type for this request.
     * @return $this
     */
    public function setContentType($value)
    {
        $this->_contentType = $value;

        return $this;
    }

    /**
     * @param string $value user agent.
     * @return $this
     */
    public function setUserAgent($value)
    {
        $this->_userAgent = $value;

        return $this;
    }

    /**
     * @param string $username username for http auth.
     * @param string $password password for http auth.
     * @return $this
     */
    public function setHttpAuth($username, $password)
    {
        $this->_httpAuth = true;
        $this->_username = $username;
        $this->_password = $password;

        return $this;
    }

    /**
     * @param bool $asObject if you need get result as object.
     * @return array|\stdClass result.
     * @throws Exception if curl request have errors.
     */
    public function call($asObject = false)
    {
        $this->_request = curl_init();

        /* Set user agent & content type. */
        curl_setopt($this->_request, CURLOPT_USERAGENT, $this->_userAgent);
        curl_setopt($this->_request, CURLOPT_HTTPHEADER, ['Content-Type:' . $this->_contentType]);
        curl_setopt($this->_request, CURLOPT_HTTPHEADER, ['Accept:' . $this->_contentType]);

        /* Call rest method. */
        $this->{$this->_method}();

        /* Optional Authentication: */
        $this->_httpAuth && $this->httpAuth();

        /* Set url for api request */
        curl_setopt($this->_request, CURLOPT_URL, $this->_url);
        curl_setopt($this->_request, CURLOPT_RETURNTRANSFER, true);

        /* Call api */
        $result = curl_exec($this->_request);

        /* Get curl errors */
        if (curl_errno($this->_request)) {
            throw new Exception(curl_error($this->_request), curl_errno($this->_request));
        }

        if ($this->_useRequestContentType) {
            /* Set request ContentType */
            $this->_requestContentType = curl_getinfo($this->_request, CURLINFO_CONTENT_TYPE);

            if ($this->_requestContentType) {
                $this->_requestContentType = explode(';', $this->_requestContentType);
                $this->_requestContentType = $this->_requestContentType[0];
            }
        }


        /* End request */
        curl_close($this->_request);

        return $this->formatResult($result, $asObject);
    }
}
