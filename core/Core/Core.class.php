<?php

abstract class Core {

	public function __construct($data = array()) {

		$this->table = self::getDbTable();

		foreach($data as $key => $value) {
			$method = $this->_setter($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function __get($key)	{
		$method = $this->_getter($key);
		if (!method_exists($this, $method)) {
            throw new Exception('Undefined getter ['.$method.'] for class '.get_class($this));
			return NULL;
        }
		return $this->$method();
	}

	public function __set($key, $value)	{
		$method = $this->_setter($key);
		if (!method_exists($this, $method)) {
			throw new Exception('Undefined setter ['.$method.'] for class '.get_class($this));
			return false;
		}
		$this->$method($value);
		return true;
	}

	protected static function getDbTable() {
		return strtolower(self::getClass());
	}

	public static function getClass() {
		return ucfirst(get_called_class());
	}

	protected function getVars() {
		return get_object_vars($this);
	}

	protected function getDbFields() {
		$vars = $this->getVars();
		unset($vars['table']);

		$fields = array();
		foreach($vars as $key => $var) {
			if (!is_array($var) && !is_object($var)) {
				$fields[$key] = $var;
			}
		}
		return $fields;
	}

	public function __toString() {
		return '<pre>'.print_r($this, true).'</pre>';
	}

	public function __set_state() {
		return '<pre>'.var_dump($this, true).'</pre>';
	}

	public function __call($method, $arguments) {
		self::_undefinedMethod('Non-static');
	}

	public function __call_static($method, $arguments) {
		self::_undefinedMethod('Static');
	}

	private function _undefinedMethod($type) {
		throw new Exception($type.' '.$method.' does not exists in class '.self::getClass().' called with arguments : '.var_dump($arguments, true));
	}

	private function _getter($key) {
		return 'get'.Utils::camelCase($key);
	}

	private function _setter($key) {
		return 'set'.Utils::camelCase($key);
	}
}