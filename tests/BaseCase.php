<?php

namespace tests;

use Tonic;
use Bazalt\Rest;

abstract class BaseCase extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function assertResponse(\Bazalt\Rest\Resource $resource, \Bazalt\Rest\Response $assertResponse)
    {
        $response = $resource->exec();
//        print_r($response);exit;

        $this->assertEquals($response->code, $assertResponse->code);
        $this->assertEquals(json_decode($response->body, true), $assertResponse->body);
    }
}