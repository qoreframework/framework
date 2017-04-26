<?php 

namespace Qore\Framework\Model;
use Qore;

abstract class ModelAbstract
{
	protected $_data = [];
	
	public function __get($key)
	{
		if(array_key_exists($key,$this->_data)
		{
			return $this->_data[$key];
		}
		return null;
	}
	
	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}
	
	public function __isset($key)
	{
		return isset($this->_data[$key]);
	}
	
	public function __unset($key)
	{
		unset($this->_data[$key]);
	}
}