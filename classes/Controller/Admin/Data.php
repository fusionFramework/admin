<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Data extends Controller_Fusion_Admin {

	public function action_index()
	{
		$this->access('admin.data.view');

		$this->_tpl = new View_Admin_Data;
		$this->_tpl->routes = [];

		if(Fusion::$user->hasAccess('admin.data.db'))
		{
			$this->_tpl->db = true;
			$this->_tpl->routes['db'] = Route::url('admin.data.db', null, true);
		}

		if(Fusion::$user->hasAccess('admin.data.app'))
		{
			$this->_tpl->app = true;
			$this->_tpl->routes['app'] = Route::url('admin.data.app', null, true);
		}

		if(Fusion::$user->hasAccess('admin.data.htdocs'))
		{
			$this->_tpl->htdocs = true;
			$this->_tpl->routes['htdocs'] = Route::url('admin.data.htdocs', null, true);
		}

		if(Fusion::$user->hasAccess('admin.data.temp'))
		{
			$this->_tpl->temp = true;
			$this->_tpl->routes['temp'] = Route::url('admin.data.temp', null, true);
		}

		if(Fusion::$user->hasAccess('admin.data.cache'))
		{
			$this->_tpl->cache = true;
			$this->_tpl->routes['cache'] = Route::url('admin.data.cache', null, true);
		}
	}

	// export DB
	public function action_db()
	{
		$this->access('admin.data.db');

		if(!$this->request->is_ajax())
		{
			Throw new HTTP_Exception_403;
		}
	}

	// Export application & modules
	public function action_app()
	{
		$this->access('admin.data.app');

		if(!$this->request->is_ajax())
		{
			Throw new HTTP_Exception_403;
		}
	}

	// Export htdocs
	public function action_htdocs()
	{
		$this->access('admin.data.htdocs');

		if(!$this->request->is_ajax())
		{
			Throw new HTTP_Exception_403;
		}
	}

	// Clear out temp files
	public function action_temp()
	{
		$this->access('admin.data.temp');

		if(!$this->request->is_ajax())
		{
			Throw new HTTP_Exception_403;
		}
	}

	// Clear out cache
	public function action_cache()
	{
		$this->access('admin.data.cache');

		if(!$this->request->is_ajax())
		{
			Throw new HTTP_Exception_403;
		}
	}
} // End Plugins admin
