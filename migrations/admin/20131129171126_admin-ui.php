<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Generate Admin routes and permissions
 */
class Migration_Admin_20131129171126 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		Minion_Task::factory(['task' => 'admin:routes']);
		Minion_Task::factory(['task' => 'admin:permissions']);
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{

	}

}
