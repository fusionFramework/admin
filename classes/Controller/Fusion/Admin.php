<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Base controller for admin pages
 *
 * @package    fusionFramework
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) 2013-2014 Maxim Kerstens
 * @license    BSD
 */
abstract class Controller_Fusion_Admin extends Controller_Fusion {

	//set the base template
	protected $_template_cfg = 'admin';

	public function before()
	{
		// Load admin definitions from Fusion modules
		$dirs = new DirectoryIterator(FUSIONPATH);

		foreach ($dirs as $fileInfo) {
			if($fileInfo->isDir())
			{
				$admin = $fileInfo->getRealPath().DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'admin.php';
				if(file_exists($admin))
				{
					require_once $admin;
				}
			}
		}

		// If no user is logged in, redirect to site's index
		if( Fusion::$user == null )
		{
			$this->redirect(Route::url('default', null, true));
		}

		// check for user access
		if( ! Fusion::$user->hasAccess('admin') )
		{
			throw new HTTP_Exception_403('You\'re not allowed to view the admin.');
		}

		parent::before();

		// Add admin assets if it's no ajax request
		if(!$this->request->is_ajax())
		{
			Fusion::$assets->add_set('admin');
		}

	}

} // End Fusion's Admin controller
