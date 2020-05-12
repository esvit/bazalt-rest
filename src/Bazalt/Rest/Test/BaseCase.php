<?php

namespace Bazalt\Rest\Test;

use Tonic;
use Bazalt\Rest;

abstract class BaseCase extends \PHPUnit\Framework\TestCase
{
    protected $app;

    protected function initApp($files)
    {
        $config = array(
            'load' => $files
        );
        $this->app = new \Tonic\Application($config);
    }

    public function send($request, $options= array())
    {
        list($method, $uri) = explode(' ', $request);

        if (!is_array($options)) {
            $options = array();
        }
        if (!isset($options['contentType'])) {
            $options['contentType'] = 'application/json';
        }
        $options['method'] = $method;
        $options['uri'] = $uri;

        $get = $_GET;
        if (strtolower($method) == 'get' && isset($options['data'])) {
            $_GET = $options['data'];
        }
        if (isset($options['_GET'])) {
            $_GET = $options['_GET'];
        }

        $request = new \Tonic\Request($options);

        $resource = $this->app->getResource($request);
        $response = $resource->exec();

        $_GET = $get;

        $body = $response->body;
        if ($options['contentType'] == 'application/json') {
            $body = json_decode($response->body, true);
        }
        return array($response->code, $body);
    }

    public function assertResponseCode($request, $options= array(), $code)
    {
        list($resCode, $response) = $this->send($request, $options);
        $this->assertEquals($code, $resCode);
    }

    public function assertResponse($request, $options= array(), \Bazalt\Rest\Response $assertResponse)
    {
        list($code, $response) = $this->send($request, $options);

        $this->assertEquals($assertResponse->body, $response);
        $this->assertEquals($assertResponse->code, $code);
    }

    public function assertRegExpResponse($request, $options= array(), \Bazalt\Rest\Response $assertResponse)
    {
        list($code, $response) = $this->send($request, $options);

        $this->assertRegExp($assertResponse->body, json_encode($response));
        $this->assertEquals($assertResponse->code, $code);
    }
}
