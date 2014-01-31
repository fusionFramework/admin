<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Generate permission files for your admin UI definitions.
 *
 * @package    fusionFramework
 * @category   Admin
 * @author     Maxim Kerstens
 */
class Task_Admin_Permissions extends Minion_Task
{
	protected function _execute(array $params)
	{
		$admins = Kohana::list_files('classes'.DIRECTORY_SEPARATOR.'Admin');

		foreach($admins as $path => $files)
		{
			$this->_parse_admin($path, $files);
		}

		foreach($this->_perms as $module => $cfg)
		{
			foreach($cfg['perms'] as $resource => $content)
			{
				$cfg['_cfg']->set($resource, $content);
			}
			$cfg['_cfg']->export($cfg['dir']);
		}

		// now process permissions for config definitions
		$cfgs = Kohana::list_files('module'.DIRECTORY_SEPARATOR.'cfg');

		//if it's the first time we're running this
		if(!is_dir(APPPATH.'permissions'))
		{
			mkdir(APPPATH.'permissions');
		}

		if(!file_exists(APPPATH.'permissions'.DIRECTORY_SEPARATOR.'admin.php'))
		{
			$old_cfg = false;
			file_put_contents(APPPATH.'permissions'.DIRECTORY_SEPARATOR.'admin.php', Kohana::FILE_SECURITY."\n\n return [];");
		}
		else
		{
			$old_cfg = include(APPPATH.'permissions'.DIRECTORY_SEPARATOR.'admin.php');
		}

		$config = new Config();
		$config = $config->attach(new Config_File('permissions'))->load('admin');

		foreach($config->getArrayCopy() as $key => $value)
		{
			$config->offsetUnset($key);
		}

		//add the old CFG
		if($old_cfg != false)
		{
			foreach($old_cfg as $key => $cfg)
			{
				$config->set($key, $cfg);
			}
		}

		//add the new permissions
		$sets = [];
		foreach($cfgs as $cfg)
		{
			$file = basename($cfg, '.php');
			if(!in_array($file, $config->config))
			{
				$sets[] = $config->config[] = $file;
			}
		}

		$config->export(APPPATH.'permissions'.DIRECTORY_SEPARATOR);

		Minion_CLI::write('Config file permissions exported:');
		Minion_CLI::write(var_export($sets, true));
	}

	protected $_perms = [];

	protected function _parse_admin($path, $files)
	{
		if(Text::ends_with($path, '.php') == true)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '_', str_replace(array('classes'.DIRECTORY_SEPARATOR, '.php'), '', $path));
			$this->add_perms($file, $path, $files);
		}
		else
		{
			foreach($files as $p => $f)
			{
				$this->_parse_admin($p, $f);
			}
		}
	}

	protected function add_perms($class, $file, $path)
	{
		$admin = new $class;

		$origin = str_replace($file, '', $path);
		$module = str_replace(FUSIONPATH, '', $origin);

		if(! array_key_exists($module, $this->_perms))
		{
			if(!file_exists($origin.'permissions'.DIRECTORY_SEPARATOR.'admin'.EXT))
			{
				mkdir($origin.'permissions'.DIRECTORY_SEPARATOR.'admin'.EXT);
				$original_perms = [];
			}
			else
			{
				$original_perms = Kohana::load($origin.'permissions'.DIRECTORY_SEPARATOR.'admin'.EXT);
			}

			$this->_perms[$module] = [
				'_cfg' => new Config(),
				'perms' => [],
				'dir' => $origin.'permissions'.DIRECTORY_SEPARATOR
			];

			$this->_perms[$module]['_cfg'] = $this->_perms[$module]['_cfg']->attach(new Config_File('permissions'))->load('admin');

			// empty the config object
			foreach($this->_perms[$module]['_cfg']->as_array() as $key => $value)
			{
				unset($this->_perms[$module]['_cfg'][$key]);
			}

			// fill with original permissions
			if(count($original_perms) > 0)
			{
				foreach($original_perms as $key => $content)
				{
					$this->_perms[$module]['_cfg']->set($key, $content);
				}
			}
		}

		$this->_perms[$module]['perms'][$admin->resource] = ['view', 'manage', 'remove'];

		if($admin->track_changes == true)
		{
			$this->_perms[$module]['perms'][$admin->resource][] = 'history';
		}

		if($admin->images != false)
		{
			$this->_perms[$module]['perms'][$admin->resource][] = 'upload';
		}

		if($admin->methods() != false)
		{
			$this->_perms[$module]['perms'][$admin->resource][] = 'methods';
		}

		if(count($admin->actions) > 0)
		{
			foreach($admin->actions as $action)
			{
				$this->_perms[$module]['perms'][$admin->resource][] = $action;
			}
		}

		Minion_CLI::write();
		Minion_CLI::write($module . ' admin.' . $admin->resource . ' perms added.');
		Minion_CLI::write(var_export($this->_perms[$module]['perms'][$admin->resource], true));
	}
}