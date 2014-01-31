<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Admin permissions for 'Admin' module
 */
return array(
	'search',
	'config' => ['view', 'edit'],
	'data' => ['view', 'store'],
	'plugins' => ['view', 'install', 'state'],
	'data' => ['view', 'db', 'app', 'htdocs', 'temp', 'cache']
);