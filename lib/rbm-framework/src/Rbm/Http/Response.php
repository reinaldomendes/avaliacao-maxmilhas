<?php

namespace Rbm\Http;

/**
 *
 */
class Response
{
    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var response status code
     */
    protected $httpResponseCode = 200;

    /**
     * set body of http response.
     * @return  Rbm\Http\Response
     */
    public function setBody($data)
    {
        $this->body = (string) $data;

        return $this;
    }
    /**
     * @param $name - string
     * @param $value - string
     * @return  Rbm\Http\Response
     */
    public function setHeader($name, $value)
    {
        $this->headers[strtolower($name)] = $value;

        return $this;
    }

    public function getHeader($name)
    {
        return $this->headers[strtolower($name)];
    }

    /**
     * return  body.
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * return string version of a response.
     * @return string
     */
    public function __toString()
    {
        return $this->getBody();
    }

    /**
     * @param string $url
     * @param int $code http status code
     * @return  Rbm\Http\Response
     */
    public function setRedirect($url, $code = '302')
    {
        return $this->setHeader('Location', $url)->setHttpResponseCode($code);
    }

    /**
     * set http status code.
     * @return  Rbm\Http\Response
     */
    public function setHttpResponseCode($code)
    {
        $this->httpResponseCode = $code;

        return $this;
    }

    /**
     * define http_status_code on php.
     * @return  Rbm\Http\Response
     */
    public function sendHttpStatusCode()
    {
        http_response_code($this->httpResponseCode);

        return $this;
    }

    /**
     *  @return  Rbm\Http\Response
     */
    public function sendHeaders()
    {
        $this->sendHttpStatusCode();
        foreach ($this->headers as $name => $value) {
            header($name.':'.$value);
        }

        return $this;
    }

    public function send()
    {
        $this->sendHeaders();
        echo $this->body;
    }
}
