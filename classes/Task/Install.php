<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A task to start the installation of fusionFramework.
 *
 * @package    fusionFramework
 * @category   Admin
 * @author     Maxim Kerstens
 */
class Task_Install extends Minion_Task
{
	protected function _execute(array $params)
	{
		// Remove the bundled plugins of the kohana-plugin-system-module
		$plugins = MODPATH.'kohana-plugin-system'.DIRECTORY_SEPARATOR.'plugins';

		if(file_exists($plugins))
		{
			$it = new RecursiveDirectoryIterator($plugins);
			$files = new RecursiveIteratorIterator($it,
				RecursiveIteratorIterator::CHILD_FIRST);
			foreach($files as $file) {
				if ($file->getFilename() === '.' || $file->getFilename() === '..') {
					continue;
				}
				if ($file->isDir()){
					rmdir($file->getRealPath());
				} else {
					unlink($file->getRealPath());
				}
			}
			rmdir($plugins);
		}

		// Do initial migrations
		Minion_Task::factory(['task' => 'migrations:run']);

		// Generate admin routes
		Minion_Task::factory(['task' => 'admin:routes']);

		// Generate admin permissions
		Minion_Task::factory(['task' => 'admin:permissions']);

		// Add the first user
		$username = Minion_CLI::read('What username do you want?');
		$password = Minion_CLI::read('What password do you want?');
		$email = Minion_CLI::read('What\'s your email address?');

		ORM::factory('User')
			->values(['username' => $username, 'password' => $password, 'email' => $email, 'activated' => 1, 'activated_at' => time()])
			->save()
			->config('points', Fusion::$config['currency']['initial_budget'], true)
			->add('groups', [1,2]);

		Minion_CLI::write(Minion_CLI::color('User "'.$username.'" created successfully!', 'green'));

		Minion_CLI::write("Installation complete.");

		Minion_CLI::write("Don't forget to setup crons before going live.");
	}
}