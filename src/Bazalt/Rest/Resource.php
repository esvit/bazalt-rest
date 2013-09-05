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
    }

    protected function uniqueCookie($cookie, $time = null)
    {
        if (!$time) {
            $time = 60 * 60 * 24;
        }
        $isSet = isset($_COOKIE[$cookie]);
        if (!$isSet) {
            setcookie($cookie, true, time() + $time, '/');
        }
        return !$isSet;
    }
}