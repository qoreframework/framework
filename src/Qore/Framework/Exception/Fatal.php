<?php 

namespace Qore\Framework\Exception;
use Qore;

class Fatal extends ExceptionAbstract
{
	const LOG_FILE = 'exception'.DS.'fatal';
	
	public function __construct($message, $code = 0, Exception $previous = null)
	{
		Qore::response(500);
		return parent::__construct($message, $code, $previous);
	}
}