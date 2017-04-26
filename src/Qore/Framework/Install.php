<?php 

namespace Qore\Framework;

final class Install extends \Qore\Framework\Utility\Installer
{
	protected static $_name			=	'Qore_Framework';
	protected static $_version		=	1;
	protected static $_active		=	true;
	
	protected static $_install		=	[
		1	=>	[
			'filesystem'	=>	[],
			'database'		=>	[
				'modules'	=>	
					'CREATE TABLE IF NOT EXISTS `?` ('.
						'`name` VARCHAR(100) CONSTRAINT pk_name PRIMARY KEY,'.
						'`version` INT(5) NOT NULL,'.
					')'
			]
		]
	];
}