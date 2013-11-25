<?php

namespace Bazalt\Rest;

class Resource extends \Tonic\Resource
{
    public static function params()
    {
        $query = $_SERVER['QUERY_STRING'];
        $vars = array();
        $second = array();
        foreach (explode('&', $query) as $pair) {
            list($key, $value) = explode('=', $pair);
            if ('' == trim($value)) {
                continue;
            }

            if (array_key_exists($key, $vars)) {
                if (!array_key_exists($key, $second)) {
                    $second[$key] = array($vars[$key]);
                }
                $second[$key][] = $value;
            } else {
                $vars[$key] = urldecode($value);
            }
        }
        return array_merge($vars, $second);
    }

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
}