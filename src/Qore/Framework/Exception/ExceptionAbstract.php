<?php 

namespace Qore\Framework\Exception;
use Qore;

abstract class ExceptionAbstract extends \Exception
{
	const LOG_FILE = 'exception'.DS.'general';
	
	public function __construct($message, $code = 0, Exception $previous = null)
	{
		Qore::log($message, self::LOG_FILE);
		return parent::__construct($message, $code, $previous);
	}
}