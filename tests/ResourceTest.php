<?php

namespace tests;

use Bazalt\Rest;

class ResourceTest extends \tests\BaseCase
{
    protected $view;

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testGet()
    {
        $request = new Request();
        $request->setMethod('PUT');
        $request->setPutData([]);
        $this->assertSomething(
            $this->testSubjectUsingRequest->process($request)
        );
    }

    /**
     * @expectedException Exception

    public function testFetchError()
    {
    //$this->assertEquals('-', $this->view->fetch('test-invalid'));
    }*/
}