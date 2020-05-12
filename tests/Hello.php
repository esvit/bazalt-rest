<?php

namespace tests;

use Bazalt\Rest;

/**
 * The obligitory Hello World example
 *
 * The @uri annotation routes requests that match that URL to this resource. Multiple
 * annotations allow this resource to match multiple URLs.
 *
 * @uri /hello
 * @uri /hello/:name
 */
class Hello extends \Bazalt\Rest\Resource
{
    /**
     * The @provides annotation makes method only match requests that have a suitable accept
     * header or URL extension (ie: /hello.json) and causes the response to automatically
     * contain the correct content-type response header.
     *
     * @method GET
     * @provides application/json
     * @json
     * @return \Bazalt\Rest\Response
     */
    public function sayHelloComputer()
    {
        return new \Bazalt\Rest\Response(200, array(
            'hello' => '',
            'url' => '/hello'
        ));
    }

    /**
     * All HTTP methods are supported. The @accepts annotation makes method only match if the
     * request body content-type matches.
     *
     * curl -i -H "Content-Type: application/json" -X POST -d '{"hello": "computer"}' http://localhost/www/tonic/web/hello.json
     *
     * @method POST
     * @accepts application/json
     * @provides application/json
     * @json
     * @return \Bazalt\Rest\Response
     */
    public function feedTheComputer()
    {
        return new \Bazalt\Rest\Response(200, $this->request->data);
    }

    /**
     * @method GET
     * @json
     * @action test
     */
    public function testAction()
    {
        return new \Bazalt\Rest\Response(200, 'testAction');
    }
}
