<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Generate routes for your admin UI definitions.
 *
 * @package    fusionFramework
 * @category   Admin
 * @author     Maxim Kerstens
 */
class Task_Admin_Routes extends Minion_Task
{
	protected function _execute(array $params)
	{
		$admins = Kohana::list_files('classes'.DIRECTORY_SEPARATOR.'Admin');

		foreach($admins as $path => $files)
		{
			$this->_parse_admin($path, $files);
		}

		foreach($this->_files as $file => $content)
		{
			file_put_contents($file, $content);
		}
	}

	protected $_files = [];

	protected function _parse_admin($path, $files)
	{
		if(Text::ends_with($path, '.php') == true)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '_', str_replace(array('classes'.DIRECTORY_SEPARATOR, '.php'), '', $path));
			$this->render_routes($file, $path, $files);
			Minion_CLI::write($file . ' routes rendered.');
		}
		else
		{
			foreach($files as $p => $f)
			{
				$this->_parse_admin($p, $f);
			}
		}
	}

	protected function render_routes($class, $file, $path)
	{
		$admin = new $class;

		$origin = str_replace($file, '', $path);
		if(!file_exists($origin.'module'))
		{
			mkdir($origin.'module');
		}

		$renderer = Kostache_Layout::factory('empty');
		$tpl = new View_Task_Route();

		$tpl->resource = $admin->resource;
		$tpl->url = str_replace('.', '/', $admin->resource);
		$tpl->class = $class;
		$tpl->track_changes = $admin->track_changes;
		$tpl->title = $admin->title;
		$tpl->perform = ($admin->methods() != false);
		$tpl->upload = ($admin->images != false);
		$tpl->actions = [];

		if(count($admin->actions) > 0)
		{
			foreach($admin->actions as $action)
			{
				$tpl->actions[] = ['name' => $action];
			}
		}

		if(! array_key_exists($origin.'module'.DIRECTORY_SEPARATOR.'routes.php', $this->_files))
		{
			$this->_files[$origin.'module'.DIRECTORY_SEPARATOR.'routes.php'] = "<?php \n\n";
		}
		else
		{
			$this->_files[$origin.'module'.DIRECTORY_SEPARATOR.'routes.php'] .= "\n\n";
		}

		$this->_files[$origin.'module'.DIRECTORY_SEPARATOR.'routes.php'] .= $renderer->render($tpl);
	}
}