<?php

namespace Qore\Framework\Utility;
use Qore;

class Installer extends UtilityAbstract
{
	protected static $_name;
	protected static $_version;
	protected static $_active;
	protected static $_install;
	
	/**
	 * Installs the module as defined
	 */
	public static function install()
	{		
		if(!static::$_active)
		{
			throw new Qore\Framework\Exception\Installer('['.self::name().'] Module disabled!');
		}
	
		if(!static::$_name || !static::$_version)
		{
			throw new Qore\Framework\Exception\Installer('['.self::name().'] A name and version must be set for a module to install properly.');
		}
		
		if(!is_int(static::$_version))
		{
			throw new Qore\Framework\Exception\Installer('['.self::name().'] Versions must be integers.');
		}
		
		//Check installed version
		$installed = static::version();
		
		if(is_array(static::$_install))
		{
			foreach(static::$_install as $version => $install)
			{
				if($version < $installed)
				{
					continue;
				}
				
				if(array_key_exists('filesystem',$install) && is_array($install['filesystem']))
				{
					foreach($install['filesystem'] as $command => $data)
					{
						switch($command)
						{
							case 'mkdir':
							case 'rmdir':
							case 'touch':
							case 'rm':
							default:
								throw new Qore\Framework\Exception\Installer('['.self::name().'] Unsupported filesystem command requested: '.$command);
						}
					}
				}
				
				/** consider wrapping in wider try block and encapsulating each install sub-method **/
				if(array_key_exists('database',$install) && is_array($install['database']))
				{
					foreach($install['database'] as $table => $query)
					{
						try
						{
							Qore::connection()->query($query, [Qore::connection()->addTablePrefix($table)]);
						}
						catch (\Exception $e)
						{
							throw new Qore\Framework\Exception\Installer(
								'['.self::name().'] Error installing SQL for Version '.$version.', Table "'.$table.'": '.Qore::EOL.$e->getMessage()
							);
						}
					}
				}
			}
		}
		
		$this->_updateVersion();
	}
	
	protected final function _updateVersion()
	{
		Qore::connection()->table('modules')
			->onDuplicateKeyUpdate->(['version' => static::$_version])
			->insert(['name' => static::$_name, 'version' => static::$_version]);
	}
	
	/**
	 * Gets the installed version of the module
	 */
	public static function version()
	{
		$iv = Qore::moduleVersion(static::$_name);
		if($iv === false)
		{
			$iv = 0;
		}
		return $iv;
	}
	
	public static function active()
	{
		return static::$_active;
	}
	
	public static function name()
	{
		return static::$_name;
	}
}