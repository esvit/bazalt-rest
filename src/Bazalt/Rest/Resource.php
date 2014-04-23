<?php

namespace Bazalt\Rest;

class Resource extends \Tonic\Resource
{
    protected static $parsedParams = null;

    public static function params($params = null)
    {
        if($params === null) {
            if(self::$parsedParams === null) {
                self::$parsedParams = self::parseParams();
            }
            return self::$parsedParams;
        }
        self::$parsedParams = $params;
    }

    protected static function parseParams()
    {
        $query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        $vars = array();
        $second = array();
        $parts = explode('&', $query);
        foreach ($parts as $pair) {
            $current = &$vars;
            if (strpos($pair, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $pair);
            if ('' == trim($value)) {
                continue;
            }
            $key = urldecode($key);
            $tokens = explode('[', str_replace(']', '', $key));
            if (count($tokens) > 1) {
                $key = $tokens[count($tokens) - 1];
                $tokens = array_slice($tokens, 0, -1);
                foreach ($tokens as $token) {
                    if (!array_key_exists($token, $current)) {
                        $current[$token] = array();
                    }
                    $current = &$current[$token];
                }
                $key = empty($key) ? count($current) : $key;
            }
            if (array_key_exists($key, $current)) {
                if (!array_key_exists($key, $second)) {
                    $second[$key] = array($current[$key]);
                }
                $second[$key][] = $value;
            } else {
                $current[$key] = urldecode($value);
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
