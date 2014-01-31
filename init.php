<?php defined('SYSPATH') OR die('No direct script access.');

Route::set('admin', 'admin')
	->defaults(array(
		'controller' => 'Admin',
		'action'     => 'index'
	)
);

// Route temp uploaded files
Route::set('admin.tmp', 'admin/tmp/<file>', array('file' => '.*'))
	->defaults(array(
			'controller' => 'Admin',
			'action'     => 'temp'
		)
	);

Route::set('admin.search', 'admin/search/<type>/<term>.json')
	->defaults(array(
			'controller' => 'Admin',
			'action'     => 'search'
		)
	);

Route::set('admin.config.index', 'admin/config')
	->defaults(array(
			'controller' => 'Admin_Config',
			'action'     => 'index'
		)
	);
Route::set('admin.config.load', 'admin/config/load/<file>')
	->defaults(array(
			'controller' => 'Admin_Config',
			'action'     => 'load'
		)
	);
Route::set('admin.config.save', 'admin/config/save/<file>')
	->defaults(array(
			'controller' => 'Admin_Config',
			'action'     => 'save'
		)
	);
Route::set('admin.config.upload', 'admin/config/upload/<file>/<image>')
	->defaults(array(
			'controller' => 'Admin_Config',
			'action'     => 'upload'
		)
	);
Route::set('admin.data.db', 'admin/data/db')
	->defaults(array(
			'controller' => 'Admin_Data',
			'action'     => 'db'
		)
	);
Route::set('admin.data.app', 'admin/data/app')
	->defaults(array(
			'controller' => 'Admin_Data',
			'action'     => 'app'
		)
	);
Route::set('admin.data.htdocs', 'admin/data/htdocs')
	->defaults(array(
			'controller' => 'Admin_Data',
			'action'     => 'htdocs'
		)
	);
Route::set('admin.data.cache', 'admin/data/cache')
	->defaults(array(
			'controller' => 'Admin_Data',
			'action'     => 'cache'
		)
	);
Route::set('admin.data.temp', 'admin/data/temp')
	->defaults(array(
			'controller' => 'Admin_Data',
			'action'     => 'temp'
		)
	);
Route::set('admin.data.index', 'admin/data')
	->defaults(array(
			'controller' => 'Admin_Data',
			'action'     => 'index'
		)
	);

Route::set('admin.plugins.index', 'admin/plugins')
	->defaults(array(
		'controller' => 'Admin_Plugins',
		'action'     => 'index',
	));
Route::set('admin.plugins.install', 'admin/plugins/<plugin>/install')
	->defaults(array(
		'controller' => 'Admin_Plugins',
		'action'     => 'install',
	));
Route::set('admin.plugins.activate', 'admin/plugins/<plugin>/activate')
	->defaults(array(
		'controller' => 'Admin_Plugins',
		'action'     => 'activate',
	));
Route::set('admin.plugins.deactivate', 'admin/plugins/<plugin>/deactivate')
	->defaults(array(
		'controller' => 'Admin_Plugins',
		'action'     => 'deactivate',
	));
