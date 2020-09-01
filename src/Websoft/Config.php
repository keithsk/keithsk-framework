<?php

namespace Websoft;

class Config
{
	private $configs;

	public function __construct()
	{
		foreach (glob("config/*.php") as $filename)
		{
			$filePaths = pathinfo($filename);

			$this->configs[$filePaths['filename']] = include $filename;
		}
	}

	/**
	 * Usage: read('app.name', 'Default App Name');
	 */
	public function read($key, $default = null)
	{
		$value = $this->getValueByKey($key, $this->configs, $default);
		return $value;
	}

	/**
	 * Dot notation allows us to traverse an array in a very elegant way, it is specially useful when working with deeply nested sets.
	 */
	private function getValueByKey($key, array $data, $default = null)
	{
		// assert $key is a non-empty string
		// assert $data is a loopable array
		// otherwise return $default value
		if (!is_string($key) || empty($key) || !count($data))
		{
			return $default;
		}
	
		// assert $key contains a dot notated string
		if (strpos($key, '.') !== false)
		{
			$keys = explode('.', $key);
	
			foreach ($keys as $innerKey)
			{
				// assert $data[$innerKey] is available to continue
				// otherwise return $default value
				if (!array_key_exists($innerKey, $data))
				{
					return $default;
				}
	
				$data = $data[$innerKey];
			}
	
			return $data;
		}
	
		// fallback returning value of $key in $data or $default value
		return array_key_exists($key, $data) ? $data[$key] : $default;
	}

}