<?php

namespace tests;

use Bazalt\Rest;
use Tonic;

class ResourceTest extends \tests\BaseCase
{
    protected $app;

    protected function setUp()
    {
        $config = array(
            'load' => array(
                __DIR__ .'/*.php'
            )
        );
        $this->app = new Tonic\Application($config);
    }

    protected function tearDown()
    {
    }

    public function testGet()
    {
        $request = new Tonic\Request(array(
            'uri' => '/hello.json'
        ));

        $response = new \Bazalt\Rest\Response(200, array(
            'hello' => '',
            'url' => '/hello'
        ));

        $resource = new \tests\Hello($this->app, $request);

        $this->assertResponse($resource, $response);
    }


    public function testPost()
    {
        $request = new Tonic\Request(array(
            'uri' => '/hello.json',
            'method' => 'POST',
            'contentType' => 'application/json',
            'data' => json_encode(array(
                'hello' => 'computer'
            ))
        ));
        $response = new \Bazalt\Rest\Response(200, array(
            'hello' => 'computer'
        ));

        $resource = new \tests\Hello($this->app, $request);

        $this->assertResponse($resource, $response);
    }
}