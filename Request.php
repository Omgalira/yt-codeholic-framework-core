<?php

namespace Omgalira\TheCodeholicPhpMvc;

class Request
{
    private array $routeParams = [];

    public function getBaseUrl(): string
    {
        $serverProtocol = $_SERVER['REQUEST_SCHEME'] ?? 'http';
        $serverName = $_SERVER['SERVER_NAME'];
        $serverPort = $_SERVER['SERVER_PORT'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        $url = '';
        $url .= $serverProtocol.'://';
        $url .= $serverName;

        if ( ($serverProtocol === 'http' && $serverPort !== '80') || ($serverProtocol === 'https' && $serverPort !== '443')) {
            $url .= ':'.$serverPort;
        }

        $baseUri = str_replace('/index.php', '', $scriptName);

        $url .= $baseUri;

        // Buscar si se mandaron parámetros QueryString
        $pos = strpos($requestUri, '?');
        if ($pos) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $route = str_replace($baseUri, '', $requestUri);

        return $url;
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get';
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function getBody()
    {
        $body = [];

        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    public function setRouteParams($params)
    {
        $this->routeParams = $params;
        return $this;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }
}