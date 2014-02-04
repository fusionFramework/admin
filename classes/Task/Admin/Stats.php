<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A cronjob to cache site stats
 *
 * The cache is saved for 24 hours, but this cronjob can be run every 5 minutes or more,
 * depending on how real-time you want your stats to be and how much your serer can handle.
 *
 * @package    fusionFramework
 * @category   Admin
 * @author     Maxim Kerstens
 */
class Task_Admin_Stats extends Minion_Task
{
	protected function _execute(array $params)
	{
		$cache = Cache::instance();

		// Load admin definitions from Fusion modules
		$dirs = new DirectoryIterator(FUSIONPATH);
		foreach ($dirs as $fileInfo) {
			if($fileInfo->isDir())
			{
				$admin = $fileInfo->getRealPath().DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'minion.php';
				if(file_exists($admin))
				{
					require_once $admin;
				}
			}
		}

		Plug::fire('admin.task.stats', [$cache]);
	}
}