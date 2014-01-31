<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset sets (libraries and plugins)
 */
return array(
	// The set that is loaded on every page of the admin
	'admin' => array(
		'set'  => array('jquery', 'bootstrap', 'editor'),
		'css' => array(
			'admin.css'
		),
		'js' => 'admin.js'
	),
	'moveselect' => array(
		'js' => 'plugins/jquery.moveSelect.js'
	),
	'alpaca' => [
		'js' => [
			'plugins/jquery.tmpl.js',
			'alpaca.js'
		],
		'css' => [
			'alpaca.css',
			'alpaca-bootstrap.css'
		]
	]
);