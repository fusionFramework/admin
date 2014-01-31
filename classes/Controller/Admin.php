<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Admin dashboard
 *
 * @package    fusionFramework
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) 2013-2014 Maxim Kerstens
 * @license    BSD
 */
class Controller_Admin extends Controller_Fusion_Admin {

	public function action_index()
	{
		$this->_tpl = new View_Admin_Dashboard;
		$cache = Cache::instance();

		$this->_tpl->stats = Plug::fire('admin.dashboard.stats', [$cache]);
	}

	public function action_search()
	{
		//throw 404 if it's no ajax request
		if(!$this->request->is_ajax())
		{
			throw new HTTP_Exception_404;
		}

		$this->access('admin.search');

		$data = ['type' => $this->request->param('type'), 'term' => $this->request->param('term'), 'handle' => 'data'];

		$response = Plug::fire('admin.search', $data);

		$results = array();
		foreach($response as $resp)
		{
			if(is_array($resp))
			{
				$results = $results + $resp;
			}
		}
		$this->_handle_ajax = false;

		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($results));
	}

	public function action_temp()
	{
		$file = DOCROOT.'media'.DIRECTORY_SEPARATOR.$this->request->param('file');

		if(!file_exists($file))
			throw new Kohana_HTTP_Exception_404;

		// Send the file content as the response
		$this->response->body(file_get_contents($file));

		$this->response->headers('Content-Type', (string) File::mime_by_ext(pathinfo($file, PATHINFO_EXTENSION)));
		$this->response->headers('Content-Length', (string) filesize($file));
		$this->response->headers('Last-Modified', (string) date('r', filemtime($file)));
	}

} // End Admin dashboard
