<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Table extends View_Admin {
	public $header = '';
	public $icon = '';

	public $table;

	public $display_info = false;
	public $info_modal = null;
	public $history = false;
	public $methods = false;

	public $modal = null;

	public function has_toolbar() {
		if($this->display_info != false)
		{
			return true;
		}
		if($this->methods != false)
		{
			return true;
		}

		return false;
	}

	public function has_methods() {
		if($this->methods != false)
		{
			return true;
		}

		return false;
	}

	public function methods()
	{
		$return = [];

		foreach($this->methods as $key => $value)
		{
			$return[] = ['name' => $key, 'value' => $value];
		}
		return $return;
	}
}
