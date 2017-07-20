<?php 
namespace Qore;

final class App
{
	/** Data **/
	private static $__version		= '0.1.0-dev';
	private static $__config		= [];
	private static $__modules		= [];
	private static $__connection	= null;
	private static $__qb			= null;
	
	/** Constants **/
	const MODULE_STATUS_DISABLED	=	0;
	const MODULE_STATUS_ENABLED		=	1;
	const MODULE_STATUS_DEV			=	2;
	
	/** Logging Constants **/
	const EOL						= "\n";
	const LOG_PATH					= 'logs';
	const LOG_EXTENSION				= '.log';
	
	
	public static function version()
	{
		return self::__version;
	}
	
	public static function log($message, $file)
	{
		file_put_contents($message . self::EOL, FS_ROOT . DS . self::LOG_PATH . DS . $file . self::LOG_EXTENSION, FILE_APPEND);
	}
	
	public static function connection()
	{
		if(!(self::$__connection instanceof \Pixie\Connection))
		{
			try
			{
				$config = self::config('database');
				if(!$config)
				{
					throw new Qore\Framework\Exception\Fatal('Database configuration missing.');
				}
				self::$__connection = new \Pixie\Connection($config['driver'],$config);
				self::$__qb = new \Pixie\QueryBuilder\QueryBuilderHandler(self::$__connection);
			}
			catch (Qore\Framework\Exception\Fatal $e)
			{
				throw $e;
				exit;
			}
			catch (Exception $e)
			{
				throw new Qore\Framework\Exception\Fatal('Could not connect to configured database.');
				exit;
			}
		}
		return self::$__qb;
	}
	
	public static function session($key, $value = null)
	{
		if($value !== null)
		{
			$_SESSION[$key] = $value;
		}
		
		return $_SESSION[$key];
	}
	
	public static function run()
	{
		if(self::moduleVersion('Qore_Framework') === 0)
		{
			throw new Qore\Framework\Exception\Fatal('Qore Framework has not been properly installed.');
			exit;
		}
		
		session_start();
		self::loadModules();
		self::serveRoute();
	}
	
	public static function addRoute($route, $controller, $action = false, $params = array())
	{
		
	}
	
	public static function serveRoute($route = false)
	{
		
	}
	
	public static function loadModules()
	{
		$modules = self::$__modules;
		
		//Load each modules init and config
	}
	
	public static function install($module)
	{
		$class = str_replace('_','\\',$module).'\Install';
		
		try 
		{
			return $class::install();
		} 
		catch (Exception $e)
		{
			echo 'Error installing module: '.$module."\n";
			return false;
		}
	}
	
	public static function setConfig($configFile)
	{
		if(!is_readable($configFile))
		{
			throw new Qore\Framework\Exception\Fatal('Qore Config file unable to be loaded');
		}
		self::$__config = json_decode(file_get_contents($configFile),true);
	}
	
	public static function setModules($modulesArray)
	{
		self::$__modules = $modulesArray;
	}
	
	public static function config($section,$key = false)
	{
		if(!array_key_exists($section,self::$__config))
		{
			return false;
		}
		
		if($key)
		{
			if(!array_key_exists($key, self::$__config[$section]))
			{
				return false;
			}
			
			return self::$__config[$section][$key];
		}
		return self::$__config[$section];
	}
	
	public static function moduleVersion($module)
	{
		try 
		{
			$row = self::connection()->table('modules')->find($module,'name');
			if(!$row)
			{
				throw new \Qore\Framework\Exception\Database('Module "'.$module.'" is not installed.');
			}
			return $row->version;
		}
		catch (Qore\Framework\Exception\Fatal $e)
		{
			return 0;
		}
		catch (Qore\Framework\Exception\Database $e)
		{
			return 0;
		}
		catch (\Exception $e)
		{
			return 0;
		}
	}
	
	public static function response($code)
	{
		http_response_code($code);
	}
}