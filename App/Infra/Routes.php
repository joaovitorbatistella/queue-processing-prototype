<?php

namespace Infra;

class Routes
{
    /**
     * @return array
     */
    public static function getRoutes()
    {
        $response = self::getUrls();
        $urls = $response['uri'];
        $request = [];
        $request['route'] = strtoupper($urls[0]);
        $request['resource'] = $urls[1] ?? null;
        $request['id'] = $urls[2] ?? null;
        $request['params'] = $response['params'] ?? null;
        $request['method'] = $_SERVER['REQUEST_METHOD'];
        return $request;
    }

    /**
     * @return false|string[]
     */
    public static function getUrls()
    {

        $request = str_replace('/' . DIR_PROJECT, '', $_SERVER['REQUEST_URI']);
        $request =  explode('?', $request);
        $response = [];
        $response['uri'] =  explode('/', trim($request[0], '/'));
        $response['params'] = $request[1];
        return $response;
    }
}