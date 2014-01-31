<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Config extends View_Admin {
	public $header = 'Configuration';
	public $icon = 'fa fa-cog';

	public $definitions = [];

	public function definitions()
	{
		$list = [];

		$default_path = 'module'.DIRECTORY_SEPARATOR.'cfg'.DIRECTORY_SEPARATOR;
		foreach($this->definitions as $file => $path)
		{
			$file = basename($file, '.php');

			if(Fusion::$user->hasAccess('admin.config.'.$file))
			{
				$cfg = Kohana::load($path);

				$disabled = (array_key_exists('disabled', $cfg)) ? $cfg['disabled'] : false;

				$list[] = [
					'file' => str_replace(['.php', $default_path], '', $file),
					'name' => $cfg['title'],
					'description' => $cfg['description'],
					'disabled' => $disabled
				];
			}

		}

		return $list;
	}
}
