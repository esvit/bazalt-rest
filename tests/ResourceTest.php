<?php

namespace tests;

use Bazalt\Rest;
use Tonic;

class ResourceTest extends \Bazalt\Rest\Test\BaseCase
{
    protected $app;

    protected function setUp()
    {
        global $loader;

        $config = array(
            'load' => array(
                $loader->findFile('tests\\Hello'),
            )
        );
        $this->app = new Tonic\Application($config);
    }

    protected function tearDown()
    {
    }

    public function testParams()
    {
        $_SERVER['QUERY_STRING'] = 'param1=2549&appname=appName1&appname=appName2&appname=appName3&appname=appName4&appname=appName5&apptype=thetype&idsess=1231324567980147dzeze55sd4&action=myaction';

        $this->assertEquals(array (
            'param1' => '2549',
            'appname' =>
                array (
                    0 => 'appName1',
                    1 => 'appName2',
                    2 => 'appName3',
                    3 => 'appName4',
                    4 => 'appName5',
                ),
            'apptype' => 'thetype',
            'idsess' => '1231324567980147dzeze55sd4',
            'action' => 'myaction',
        ), \Bazalt\Rest\Resource::params());
    }

    public function testGet()
    {
        $response = new \Bazalt\Rest\Response(200, array(
            'hello' => '',
            'url' => '/hello'
        ));

        $this->assertResponse('GET /hello.json', array(), $response);
    }


    public function testPost()
    {
        $response = new \Bazalt\Rest\Response(200, array(
            'hello' => 'computer'
        ));

        $this->assertResponse('POST /hello.json', array(
            'data' => json_encode(array(
                'hello' => 'computer'
            ))
        ), $response);
    }

    public function testAction()
    {
        $response = new \Bazalt\Rest\Response(200, 'testAction');

        $_GET = array('action' => 'test');
        $this->assertResponse('GET /hello.json', array(), $response);
    }
}