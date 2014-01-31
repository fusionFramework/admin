<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Dashboard extends View_Admin {
	public $header = 'Dashboard';
	public $icon = 'dashboard';

	public function news()
	{
		try {
			$guzzle = new Guzzle\Http\Client("http://fusion.happydemon.org");
			$request = $guzzle->get("/updates.json")->send();

			return $request->json();
		}
		catch(Guzzle\Http\Exception\ServerErrorResponseException $e)
		{
			return false;
		}
	}
}
