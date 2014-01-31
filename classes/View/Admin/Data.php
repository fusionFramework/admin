<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Data extends View_Admin {
	public $header = 'Data';
	public $icon = 'fa fa-hdd-o';

	public $routes = [];

	public function routes()
	{
		return json_encode($this->routes, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
	}

	public $db = false;
	public $app = false;
	public $htdocs = false;
	public $cache = false;
	public $temp = false;
}
