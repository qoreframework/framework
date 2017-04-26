<?php 

namespace Qore\Framework\Model\Database;
use Qore;

abstract class ModelAbstract extends \Qore\Framework\Model\ModelAbstract
{
	protected $_id;
	protected $_table;
	protected $_idField;
	
	public function __construct()
	{
		$this->_validate();
	}
	
	protected function _validate()
	{
		if(!$this->_table || !$this->_idField || !is_array($this->databaseFields()))
		{
			throw new Qore\Framework\Exception\Database('Database Model '.get_class($this).' is missing required config information!');
		}
	}
	
	public function id()
	{
		return $this->{$this->_idField};
	}
	
	public function save()
	{
		$this->_validate();
		
		$modelData = [];
		
		foreach($this->databaseFields() as $field)
		{
			$modelData[$field] = $this->$field;
		}
		
		if($this->id())
		{
			Qore::connection()->table($this->_table)->where($this->_idField, $this->id())->update($modelData);
		}
		else
		{
			$this->{$this->_idField} = Qore::connection()->table($this->_table)->insert($modelData);
		}
	}
	
	public function load($id)
	{
		$this->_validate();
		
		$row = Qore::connection()->table($this->_table)->find($id,$this->_idField);
		
		if($row !== null)
		{
			foreach($this->databaseFields() as $field)
			{
				$this->$field = $row->$field;
			}
			
			$this->{$this->_idField} = $id;
		}
	}
	
	abstract function databaseFields();
}