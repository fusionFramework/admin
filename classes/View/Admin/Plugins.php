<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Plugins extends View_Admin {
	public $header = 'Plugins';
	public $icon = 'fa fa-puzzle-piece';

	public $plugins = [];

	public function plugins()
	{
		$list = [];

		foreach($this->plugins as $plugin_name => $data) {
			$return = ['name' => $plugin_name];

			Plugins::instance()->load_plugin($plugin_name);
			$plugin = Plugins::$plugins_pool[$plugin_name];

			$return['info'] = $plugin['instance']->info;

			$manager = Plugins::$manager->get($plugin_name);
			if($manager == false)
			{
				//add the plugin to the list
				Plugins::$manager->add($plugin_name);
			}

			$return['active'] = ($manager == false) ? false : $manager['active'];
			$return['status'] = ($return['active'] == false) ? ['text' => 'activate', 'link' => Route::url('admin.plugins.activate', ['plugin' => $plugin_name], true)]
				: ['text' => 'deactivate', 'link' => Route::url('admin.plugins.deactivate', ['plugin' => $plugin_name], true)];

			$return['installed'] = ($manager == false) ? false : $manager['installed'];
			$return['install'] = Route::url('admin.plugins.install', ['plugin' => $plugin_name], true);

			$list[] = $return;
		}

		return $list;
	}
}
