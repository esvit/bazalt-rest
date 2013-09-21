<?php

namespace Bazalt\Rest;

class Resource extends \Tonic\Resource
{
    /**
     * Condition method to turn output into JSON
     */
    protected function json()
    {
        $this->before(function ($request) {
            if ($request->contentType == "application/json") {
                $request->data = json_decode($request->data);
            }
        });
        $this->after(function ($response) {
            $response->contentType = "application/json";

            if (isset($_GET['jsonp'])) {
                $response->body = $_GET['jsonp'] . '(' . json_encode($response->body) . ');';
            } else {
                $response->body = json_encode($response->body);
            }
        });
    }

    protected function action($action)
    {
        if (!isset($_GET['action']) || $_GET['action'] != $action) {
            throw new \Tonic\ConditionException;
        }
        return 100;
    }

    /**
     * @method GET
     * @provides text/html
     * @accepts text/html
     * @priority 100
     * @return \Bazalt\Rest\Response
     */
    public function getDocumentation()
    {
        $resourceMetadata = $this->app->getResourceMetadata($this);

        // get data from reflector
        $classReflector = new \ReflectionClass($resourceMetadata['class']);
        $comment = $this->parseDocComment($classReflector->getDocComment());

        $doc = $resourceMetadata['class'] . "\n" . str_repeat('_', strlen($resourceMetadata['class'])) . "\n";

        $doc .= $comment['comment'];

        $html = (class_exists('\\Michelf\\Markdown')) ? \Michelf\Markdown::defaultTransform($doc) : $doc;

        return new \Bazalt\Rest\Response(200, $html);
    }

    /**
     * Parse annotations out of a doc comment
     * @param  str   $comment Doc comment to parse
     * @return str[]
     */
    private function parseDocComment($comment)
    {
        $data = array();
        preg_match_all('/^\s*\*[*\s]*(@.+)$/m', $comment, $items);
        if ($items && isset($items[1])) {
            foreach ($items[1] as $item) {
                preg_match_all('/"[^"]+"|[^\s]+/', $item, $parts);
                $key = array_shift($parts[0]);
                array_walk($parts[0], create_function('&$v', '$v = trim($v, \'"\');'));
                $data[$key][] = $parts[0];
            }
        }

        $lines = explode("\n", trim($comment, '/* '));
        $doc = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $line = trim(trim($line, '*'));
                if (!empty($line) && $line[0] == '@') {
                    break;
                }
                $doc .= $line . "\n";
            }
        }
        $data['comment'] = $doc;
        return $data;
    }
}