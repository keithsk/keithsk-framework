<?php

namespace Websoft;

use Exception;

class Response
{
    public $content;
    public $status;
    public $headers;

	public function __construct()
	{
        //
    }
    
    public function json($content = '', $status = 200, array $headers = [])
	{
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;

        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($content);

        exit();
    }
    
}