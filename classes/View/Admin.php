<?php defined('SYSPATH') OR die('No direct script access.');

abstract class View_Admin extends Views {
	public $header = 'Admin';
	public $icon = false;

	public function nav()
	{
		$admin = [
			[
				'title' => 'Dashboard',
				'route'  => 'admin',
				'icon'  => 'fa fa-dashboard',
			]
		];
		$nav = new Element(['active_item_class' => 'active', 'items' => array_merge($admin, array_reverse(Plug::fire('admin.nav_list')))]);

		return $nav->render('Menu', 'admin');
	}

	public function nav_top()
	{
		$links = array();

		if(Fusion::$user->hasAccess('admin.plugins.view'))
		{
			$links[] = array(
				'url' => Route::url('admin.plugins.index', null, true),
				'icon' => 'puzzle-piece',
				'text' => 'Plugins'
			);
		}

		if(Fusion::$user->hasAccess('admin.config.view'))
		{
			$links[] = array(
				'url' => Route::url('admin.config.index', null, true),
				'icon' => 'cog',
				'text' => 'Config'
			);
		}

		if(Fusion::$user->hasAccess('admin.data.view'))
		{
			$links[] = array(
				'url' => Route::url('admin.data.index', null, true),
				'icon' => 'hdd-o',
				'text' => 'Data'
			);
		}

		return $links;
	}

	public function avatar()
	{
		return Fusion::$user->avatar();
	}


}
