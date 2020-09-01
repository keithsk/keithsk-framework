<?php

namespace Keithsk;

use Exception;

class Request
{

	public function __construct($routeVars)
	{
        // set Route properties to Request properties
        foreach($routeVars as $routeVarKey => $routeVar) {
            $this->$routeVarKey = $routeVar;
        }
    }
    

}