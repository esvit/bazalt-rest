<?php

namespace Bazalt\Rest\Test;

use Tonic;
use Bazalt\Rest;

abstract class BaseCase extends \PHPUnit_Framework_TestCase
{
    protected $app;

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

        $request = new \Tonic\Request($options);

        $resource = $this->app->getResource($request);
        $response = $resource->exec();

        $body = $response->body;
        if ($options['contentType'] == 'application/json') {
            $body = json_decode($response->body, true);
        }
        return [$response->code, $body];
    }

    public function assertResponse($request, $options= array(), \Bazalt\Rest\Response $assertResponse)
    {
        list($code, $response) = $this->send($request, $options);

        $this->assertEquals($assertResponse->body, $response);
        $this->assertEquals($assertResponse->code, $code);
    }
}