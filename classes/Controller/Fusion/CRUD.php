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
class Controller_Fusion_CRUD extends Controller_Fusion_Admin
{
	/**
	 * A Table instance
	 * @var Table
	 */
	public $table = null;

	protected $_template = 'View_Admin_Table';

	/**
	 * @var Admin
	 */
	protected $_admin = null;

	protected $_resource = '';

	public function before() {
		parent::before();
		$admin_class = $this->request->param('master');

		$this->_admin = new $admin_class;

		$this->_resource = str_replace('.', '_', $this->_admin->resource);
	}

	protected function _table()
	{
		// Setup the table
		$this->table = $this->_admin->setup_table(new Table);
		$this->table->name($this->_resource);
		$this->table->show_buttons(true);
	}

	/**
	 * Render the provided table
	 */
	public function action_table() {
		$this->access('admin.'.strtolower($this->_admin->resource).'.view');

		// Add all the required assets
		$this->_admin->assets();

		$this->_tpl = new $this->_template;

		$this->_table();

		if($this->_admin->methods() != false)
		{
			$this->table->cfg('checkbox', true);
		}

		$this->_tpl->table = $this->table->template_table($this->request->param('id'));
		$this->_tpl->header = $this->_admin->title;
		$this->_tpl->icon = $this->_admin->icon;

		$this->_tpl->methods = $this->_admin->methods();
		$this->_tpl->history = $this->_admin->track_changes;

		$info = $this->_admin->info();

		if($info != false)
		{
			$this->_tpl->display_info = true;
			$this->_tpl->info_modal = $info;
		}

		// Render the modal that handles the record's data
		$modal_view_data = array(
			'resource' => $this->_resource,
			'entity' => Inflector::singular($this->_admin->title),
			'model' => $this->_admin->model,
			'history' => $this->_admin->track_changes
		);

		// Get the definition
		$modal = $this->_admin->modal($modal_view_data);

		// Formo instance
		if(is_a($modal, 'Formo'))
		{
			$modal->set('attr.id', 'form-'.$this->_resource);
			if($this->_admin->images != false)
			{
				foreach($this->_admin->images as $field => $def)
				{
					$modal->$field->add_class('image-upload');
				}
			}
			$modal_view_data['body'] = $modal->render('bootstrap/form_template');
		}
		//View instance
		else if(is_a($modal, 'View'))
		{
			$modal_view_data['body'] = $modal->render();
		}
		// Mustache template
		else if(is_object($modal))
		{
			$renderer = Kostache_Layout::factory('empty');

			$modal->resource = $modal_view_data['resource'];
			$modal->entity = $modal_view_data['entity'];
			$modal->model = $modal_view_data['model'];

			$modal_view_data['body'] = $renderer->render($modal);
		}
		//String
		else
		{
			$modal_view_data['body'] = $modal;
		}

		// Render it
		$this->_tpl->modal = View::factory('admin/table_modal_layout', $modal_view_data)
			->render();
	}

	/**
	 * Create a javascript that stores the dataTable setup json
	 */
	public function action_js(){
		$this->_table();

		$this->response->headers('Content-Type','application/x-javascript');
		$this->response->body($this->table->js(Route::url('admin.'.strtolower($this->_admin->resource).'.fill', null, true)));
	}

	/**
	 * Create a javascript that handles the options buttons of each record
	 */
	public function action_js_actions(){
		$this->_table();

		$this->response->headers('Content-Type','application/x-javascript');
		$this->response->body($this->table->js_actions([
			'resource' => $this->_resource,
			'modal' => $this->_admin->modal,
			'history' => $this->_admin->track_changes,
			'images' => $this->_admin->images
		]));
	}

	/**
	 * Handle a request sent by the dataTable plugin
	 * @throws HTTP_Exception_500
	 */
	public function action_fill_table() {
		$this->access('admin.'.strtolower($this->_admin->resource).'.view');

		$this->_handle_ajax = false;

		if (DataTables::is_request())
		{
			$this->_table();

			// If we're filtering, check for an id to limit results
			if($this->_admin->filter != false && $this->request->param('id', 0) != 0)
			{
				$this->_admin->model->where($this->_admin->filter, '=', $this->request->param('id'));
			}

			$data = $this->table->model($this->_admin->model)->request();
			//set a model and render
			$this->response
				->headers('content-type', 'application/json')
				->body($data->render());
		}
		else
			throw new HTTP_Exception_500();
	}

	/**
	 * Remove a record from this resource.
	 *
	 * @throws Kohana_HTTP_Exception_404
	 */
	public function action_remove()
	{
		$this->access('admin.'.strtolower($this->_admin->resource).'.remove');
		$id = $this->request->param('id');

		if(!$this->request->is_ajax())
		{
			throw new Kohana_HTTP_Exception_404;
		}

		// Needs to be a GET request
		if($this->request->method() != Request::GET)
		{
			throw new Kohana_HTTP_Exception_403('No data was requested to be removed.');
		}

		$record = $this->_admin->model->where($this->_admin->primary_key, '=', $id)->find();

		if($record->loaded())
		{
			RD::set(RD::SUCCESS, ':resource #:id has been removed.', array(
				':resource' => Inflector::singular($this->_resource),
				':id' => $id
			));
			$record->delete();
		}
		else
		{
			RD::set(RD::WARNING, ':resource can\'t be found.', array(
				':resource' => Inflector::singular($this->_resource),
				':id' => $id
			));
		}
	}

	/**
	 * Load data when opening a modal.
	 *
	 * @throws Kohana_HTTP_Exception_404
	 */
	public function action_modal()
	{
		$this->access('admin.'.strtolower($this->_admin->resource).'.manage');
		$id = $this->request->param('id');

		if(!$this->request->is_ajax())
		{
			throw new Kohana_HTTP_Exception_404;
		}

		// Needs to be a GET request
		if($this->request->method() != Request::GET)
		{
			throw new Kohana_HTTP_Exception_403('No data was requested.');
		}

		$record = $this->_admin->model->where($this->_admin->primary_key, '=', $id)->find();

		if($record->loaded())
		{
			//set the images' full path
			$images = [];
			if($this->_admin->images != false && count($this->_admin->images) > 0)
			{
				foreach($this->_admin->images as $name => $def)
				{
					$images[$name] = call_user_func($def['web'], $record);
				}
			}
			$orm = $record->as_array();
			RD::set(RD::SUCCESS, ':resource #:id has loaded.', array(
					':resource' => Inflector::singular($this->_resource),
					':id' => $id
				),
				array_merge($orm, $images, $this->_admin->load($record))
			);
		}
		else
		{
			RD::set(RD::WARNING, ':resource can\'t be found.', array(
				':resource' => Inflector::singular($this->_resource),
				':id' => $id
			));
		}
	}

	/**
	 * Handle a modal's submit and save a record.
	 *
	 * @throws Kohana_HTTP_Exception_403
	 * @throws Kohana_HTTP_Exception_404
	 */
	public function action_save()
	{
		$this->access('admin.'.strtolower($this->_admin->resource).'.manage');
		// Needs to be an AJAX request
		if(!$this->request->is_ajax())
		{
			throw new Kohana_HTTP_Exception_404;
		}

		// Needs to be a POST request
		if($this->request->method() != Request::POST)
		{
			throw new Kohana_HTTP_Exception_403('No data was submitted.');
		}

		// Get the submitted values
		$values = $this->request->post();

		// Default model namespace
		$namespace = $this->_admin->model->object_name();

		// Get a fresh model
		$record = $this->_admin->model->clear();

		// If an id is supplied load the record, we're editing
		if(isset($values['id']) && Valid::digit($values['id']))
		{
			$record = $this->_admin->model->where($this->_admin->primary_key, '=', $values['id'])->find();
		}

		$db = Database::instance();
		try {
			$db->begin();
			$files = [];
			$save = $this->_admin->save($record, $values, $namespace);

			if(is_array($save))
			{
				$values = $save;
				$save = true;
			}

			if($save == true)
			{
				 $record->values($values[$namespace]);

				// Let's check for images
				if ($this->_admin->images != false && count($this->_admin->images) > 0)
				{
					foreach($this->_admin->images as $name => $def)
					{
						if(isset($values[$namespace][$name]))
						{
							$files[] = $tmp_file = DOCROOT.'media'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.$this->_resource.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$values[$namespace][$name];

							if(!file_exists($tmp_file))
							{
								throw new Kohana_Exception('Image :type wasn\'t uploaded properly', array(':type' => $name));
							}

							//copy the file to the correct spot
							call_user_func($def['move'], $record, $tmp_file);
						}
					}
				}

				$record->save($this->_admin->extra_validation, $this->_admin->track_changes);

			}

			$db->commit();

			foreach ($files as $tmp_file)
			{
				// if copy was used delete the temp file
				if(file_exists($tmp_file))
				{
					unlink($tmp_file);
				}
			}

			RD::set(RD::SUCCESS, 'Record #:id saved successfully', array(
				':id' => $record->pk(),
				':resource' => Inflector::humanize($this->_resource)
			), array(
				'id' => $record->pk(),
				'values' => $values
			));
		}
		catch(ORM_Validation_Exception $e)
		{
			RD::set(RD::ERROR, 'Problem validating form', null, array('errors' => $e->errors('orm'), 'submit_data' => $values));
			$db->rollback();
		}
		catch(Kohana_Exception $e)
		{
			RD::set(RD::ERROR, 'Problem validating data', null, array('errors' => [$e->getMessage()]));
			$db->rollback();
		}
	}

	public function action_upload()
	{
		// Needs to be a POST request
		if($this->request->method() != Request::POST)
		{
			throw new Kohana_HTTP_Exception_403('No data was submitted.');
		}

		$resource_dir = DOCROOT.'media'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.$this->_resource;

		if(!file_exists($resource_dir))
		{
			mkdir($resource_dir);
		}

		$type = str_replace(['[', ']', $this->_admin->model->object_name()], '', $_POST['type']);

		if(array_key_exists($type, $this->_admin->images))
		{
			$upload_dir = DIRECTORY_SEPARATOR . $type;

			if(!file_exists($resource_dir.$upload_dir))
			{
				mkdir($resource_dir.$upload_dir);
			}

			$file_name = Text::random() . '.png';
			$return = Upload::save($_FILES['Filedata'], $file_name, $resource_dir.$upload_dir);

			if($return != false)
			{
				$url = Route::url('admin.tmp', array('file' => 'admin'.DIRECTORY_SEPARATOR.$this->_resource.$upload_dir.DIRECTORY_SEPARATOR.$file_name), true);
				$this->response->body($file_name.'*/*'.$url);
			}
			else
			{
				throw new Kohana_HTTP_Exception_400;
			}
		}
		else
			throw new Kohana_HTTP_Exception_404;
	}

	/**
	 * Perform a method on multiple records.
	 *
	 * @throws Kohana_HTTP_Exception_403
	 * @throws Kohana_HTTP_Exception_404
	 */
	public function action_perform()
	{
		$this->access('admin.'.strtolower($this->_admin->resource).'.perform');

		// Needs to be an AJAX request
		if(!$this->request->is_ajax())
		{
			throw new Kohana_HTTP_Exception_404;
		}

		// Needs to be a POST request
		if($this->request->method() != Request::POST)
		{
			throw new Kohana_HTTP_Exception_403('No data was submitted.');
		}

		// Get the submitted values
		$values = $this->request->post();

		$db = Database::instance();

		try {
			$db->begin();

			$msg = $this->_admin->method($values['action'], $values['records']);

			RD::set(RD::SUCCESS, 'Action ":action" performed successfully on :total record(s)!', array(
				':action' => $values['action'],
				':total' => count($values['records'])
			), array(
				'records' => $values['records'],
				'return' => $msg
			));

			$db->commit();
		}
		catch(ORM_Validation_Exception $e)
		{
			RD::set(RD::ERROR, 'Problem validating keys', null, array('errors' => $e->errors('orm'), 'meta' => $values));
			$db->rollback();
		}
		catch(Kohana_Exception $e)
		{
			RD::set(RD::ERROR, ':error', null, array('errors' => [$e->getMessage()], 'meta' => $values));
			$db->rollback();
		}
	}

	/**
	 * Load a record's history.
	 *
	 * @throws Kohana_HTTP_Exception_404
	 */
	public function action_history()
	{
		$this->access('admin.'.strtolower($this->_admin->resource).'.history');
		$id = $this->request->param('id');

		if(!$this->request->is_ajax())
		{
			throw new Kohana_HTTP_Exception_404;
		}

		// Needs to be a GET request
		if($this->request->method() != Request::GET)
		{
			throw new Kohana_HTTP_Exception_403('No data was requested.');
		}


		$record = $this->_admin->model->where($this->_admin->primary_key, '=', $id)->find();

		if($record->loaded())
		{
			$tag_fields = array(
				'old_value',
				'new_value',
				'column_name'
			);
			$tags_group_sql = DB::expr('CAST('.implode(' AS CHAR),\'|\',CAST(', $tag_fields).' AS CHAR)');

			$datalog = ORM::factory('DataLog')
				->select(DB::expr('GROUP_CONCAT('.$tags_group_sql.' SEPARATOR \'$*/*$\') concat'))
				->where('table_name', '=', $this->_admin->model->table_name())
				->and_where('row_pk', '=', $id)
				->group_by('date_and_time')
				->order_by('date_and_time', 'DESC')
				->order_by('id', 'DESC')
				->find_all();

			$history = [];

			foreach($datalog as $data)
			{
				$history[] = $data->as_array();
			}
			RD::set(RD::SUCCESS, 'History for :resource #:id has loaded.', array(
					':resource' => Inflector::singular($this->_resource),
					':id' => $id
				),
				array('history' => $history)
			);
		}
		else
		{
			RD::set(RD::WARNING, 'History for :resource can\'t be found.', array(
				':resource' => Inflector::singular($this->_resource),
				':id' => $id
			));
		}
	}

	/**
	 * Proxy requests to the Admin instance
	 */
	public function action_proxy()
	{
		$remote_action = $this->request->param('admin_action');

		//make sure the user is allowed to make use of this proxy
		$this->access('admin.'.strtolower($this->_admin->resource).'.'.$remote_action);

		$action = 'action_'.$remote_action;

		$this->_admin->$action($this->request, $this->response);
	}

} // End Fusion_CRUD

