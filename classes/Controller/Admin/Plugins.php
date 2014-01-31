<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Plugins extends Controller_Fusion_Admin {

	public function action_index()
	{
		$this->access('admin.plugins.view');

		$this->_tpl = new View_Admin_Plugins;
		$this->_tpl->plugins = Plugins::$plugins_pool;
	}

	public function action_install() {
		$this->access('admin.plugins.install');

		$plugin = $this->request->param('plugin');
		$msg = [];

		if(array_key_exists($plugin, Plugins::$plugins_pool))
		{
			$plug = Plugins::instance()->load_plugin($plugin);

			if(Plugins::$manager->is_installed($plugin) == true)
			{
				$msg['type'] = 'warning';
				$msg['content'] = $plug->info['name'].' is already installed';
			}
			else if(Plugins::$manager->install($plugin))
			{
				$msg['type'] = 'success';
				$msg['content'] = $plug->info['name'].' was successfully installed.';
			}
			else
			{
				$msg['type'] = 'warning';
				$msg['content'] = 'There was an error installing '.$plug->info['name'];
			}
		}
		else
		{
			$msg['type'] = 'danger';
			$msg['content'] = $plugin.' does not seem to exist.';
		}
		
		RD::set($msg['type'], $msg['content']);
		
		if(!$this->request->is_ajax())
		{
			$this->redirect(Route::url('admin.plugins.index', null, true));
		}
	}

	public function action_activate() {
		$this->access('admin.plugins.state');
		$plugin = $this->request->param('plugin');
		$msg = array('title' => 'Activation');

		if(array_key_exists($plugin, Plugins::$plugins_pool))
		{
			$plug = Plugins::instance()->load_plugin($plugin);

			if(Plugins::$manager->is_active($plugin) == true)
			{
				$msg['class'] = 'warning';
				$msg['content'] = $plug->info['name'].' is already active';
			}
			else if(Plugins::$manager->activate($plugin))
			{
				$msg['class'] = 'success';
				$msg['content'] = $plug->info['name'].' was successfully activated.';
			}
			else
			{
				$msg['class'] = 'warning';
				$msg['content'] = 'There was an error activating '.$plug->info['name'];
			}
		}
		else
		{
			$msg['class'] = 'danger';
			$msg['content'] = $plugin.' does not seem to exist.';
		}

		RD::set($msg['class'], $msg['content']);

		if(!$this->request->is_ajax())
		{
			$this->redirect(Route::url('admin.plugins.index', null, true));
		}
	}

	public function action_deactivate() {
		$this->access('admin.plugins.state');

		$plugin = $this->request->param('plugin');
		$msg = array('title' => 'Deactivation');

		if(array_key_exists($plugin, Plugins::$plugins_pool))
		{
			$plug = Plugins::instance()->load_plugin($plugin);

			if(Plugins::$manager->is_active($plugin) == false)
			{
				$msg['class'] = 'warning';
				$msg['content'] = $plug->info['name'].' is not active';
			}
			else if(Plugins::$manager->deactivate($plugin))
			{
				$msg['class'] = 'success';
				$msg['content'] = $plug->info['name'].' was successfully deactivated.';
			}
			else
			{
				$msg['class'] = 'warning';
				$msg['content'] = 'There was an error deactivating '.$plug->info['name'];
			}
		}
		else
		{
			$msg['class'] = 'danger';
			$msg['content'] = $plugin.' does not seem to exist.';
		}

		RD::set($msg['class'], $msg['content']);

		if(!$this->request->is_ajax())
		{
			$this->redirect(Route::url('admin.plugins.index', null, true));
		}
	}

} // End Plugins admin
