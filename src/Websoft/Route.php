<?php

namespace Keithsk;

use Exception;

use Keithsk\Request;

class Route
{
    public $baseUrl;
    public $routeMethod;
    public $routeUri;

	public function __construct()
	{
        $this->init();
    }
    
    public function init()
	{
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $basePath = get_config('app.base_path');
        $baseUrl = $protocol . $_SERVER['HTTP_HOST'] . $basePath;

        $requestUri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        $routePageUri = str_replace($basePath, "", $requestUri);

        $routeUri = trim($routePageUri, '/'); // Strip trailing and leading slashes
        $routeMethod = $_SERVER['REQUEST_METHOD'];

        // set properties
        $this->baseUrl = $baseUrl;
        $this->routeMethod = $routeMethod;
        $this->routeUri = $routeUri;
        
    }

    public function createRoute($method, $uri, $action)
	{
        try {
            
            // assert route method
            if($this->routeMethod !== $method) {
                // throw new Exception("Invalid request method", 405);
                throw new Exception("Call next route", 200);
            }

            // assert route uri
            if($this->routeUri !== $uri) {
				throw new Exception("Call next route", 200);
            }
            
            

            // Calling callback function
            if( is_callable( $action ) ) {

                $request = new Request( get_object_vars($this) );

                $action( $request );

                exit();

            }
            else {
                throw new Exception("Invalid request callback", 400);
            }

        }
        catch(Exception $e) {
            
            if($e->getCode() == 200) {
                // Call next route
            } else {
                response()->json([
                    'status' => 'error',
                    'errorDescription' => $e->getMessage()
                ], $e->getCode());
            }

            
            
        }
    }

    public function get($uri, $action = null)
	{
        $this->createRoute('GET', $uri, $action);
    }

    public function post($uri, $action = null)
	{
        $this->createRoute('POST', $uri, $action);
    }

    public function put($uri, $action = null)
	{
        $this->createRoute('PUT', $uri, $action);
    }

    public function patch($uri, $action = null)
	{
        $this->createRoute('PATCH', $uri, $action);
    }

    public function delete($uri, $action = null)
	{
        $this->createRoute('DELETE', $uri, $action);
    }

    public function options($uri, $action = null)
	{
        $this->createRoute('OPTIONS', $uri, $action);
    }
    
}