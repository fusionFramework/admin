<?php

abstract class Admin
{
	/**
	 * ORM object we'll be managing
	 * @var ORM
	 */
	public $model = null;

	/**
	 * The model's primary key to use when managing records.
	 * If the model is loaded with relations 'id' won't suffice
	 *
	 * @var string
	 */
	public $primary_key = 'id';

	/**
	 * Extra validation rules that should be added before save
	 * @var null|Validation
	 */
	public $extra_validation = NULL;

	/**
	 * Name of the data we're handling
	 * @var string
	 */
	public $resource = '';

	/**
	 * Admin page header icon
	 * @var string
	 */
	public $icon = '';

	/**
	 * Admin page header title
	 * @var string
	 */
	public $title = '';

	/**
	 * Set options for initialising the modal
	 * @var array
	 */
	public $modal = array(
		'width' => null,
		'resize' => false,
		'height' => null,
		'maxHeight' => null,
		'modalOverflow' => false,
		'keyboard' => true
	);

	/**
	 * Should dataTable results optionally be filtered?
	 *
	 * if so, instead of false put the name of the model
	 * key you want to use to filter.
	 * @var false|string
	 */
	public $filter = false;

	/**
	 * Should we keep track of changes made to
	 * this model through the admin?
	 * @var bool
	 */
	public $track_changes = FALSE;

	/**
	 * A list containing names of image file fields,
	 * so the admin can take over the upload logic.
	 *
	 * @var false|array
	 */
	public $images = FALSE;

	/**
	 * A list of method to perform on multiple records.
	 * these are mapped to perform_$key methods,
	 * while the $value will be displayed in a select
	 *
	 * @var array
	 */
	protected $_methods = [];

	/**
	 * Return a list of available methods to apply to multiple records.
	 *
	 * @return array|false
	 */
	public function methods()
	{
		if(count($this->_methods) > 0)
			return $this->_methods;

		return false;
	}

	/**
	 * Perform a method on the selected records.
	 *
	 * These method functions should be defined in the admin class and prefixed with perform_.
	 * If an error occurs throw a Kohana_Exception with a declarative error message
	 *
	 * @param string $method Which method to load
	 * @param array  $pks    Which primary keys are used
	 */
	public function method($perform, Array $pks)
	{
		$method = 'perform_'.$perform;
		if(!method_exists($this, $method))
		{
			throw new Kohana_Exception('You are trying to apply :method to several records, but it hasn\'t been defined.', array(':method' => $perform));
		}

		return call_user_func(array($this, $method), $pks);
	}

	/**
	 * Add assets that you want to load with the table tpl
	 * @var array
	 */
	protected $_assets = [
		'set' => [],
		'js' => [],
		'css' => []
	];

	/**
	 * Add all the required assets
	 */
	public function assets()
	{
		Fusion::$assets->add_set('datatables');
		Fusion::$assets->add_js(Route::url('admin.'.$this->resource.'.js', null, true));
		Fusion::$assets->add_js(Route::url('admin.'.$this->resource.'.actions.js', null, true));

		foreach($this->_assets as $type => $defs)
		{
			if(count($defs) > 0)
			{
				Fusion::$assets->add($type, $defs);
			}
		}
	}

	public function __construct()
	{
		$this->_setup();

		if(empty($this->title))
		{
			$this->title = ucfirst(str_replace('.', ' ', $this->resource));
		}
	}

	/**
	 * Set up the dataTable definition for this controller.
	 *
	 * @see Table
	 * @param Table $table
	 * @return Table A fully configured dataTable definition
	 */
	abstract public function setup_table($table);

	/**
	 * Setup the rest of the Admin class (model, modal,...)
	 */
	abstract protected function _setup();

	/**
	 * This method gets called before data is saved to the model.
	 *
	 * Values from the form generated with Formo based on the controller's model are located in $data[$namespace].
	 *
	 * If you want to manipulate data before it's saved return the new data array (namespace it correctly).
	 * If you want to use the data to perform other actions while to original data still gets saved, just return true.
	 * If you don't want the default save to occur, return false.
	 *
	 * @param ORM       $model
	 * @param array     $data
	 * @param string    $namespace The standard model's namespace
	 * @return bool|array
	 */
	public function save(ORM $model, Array $data, $namespace)
	{
		return true;
	}

	/**
	 * Offer some info on the page (wil be displayed in a modal)
	 *
	 * @return false|string
	 */
	public function info()
	{
		return false;
	}

	/**
	 * Overwrite this method if you want to add additional data to a modal data load.
	 *
	 * @param ORM $record The model we're loading in the modal
	 * @return array
	 */
	public function load(ORM $record)
	{
		return array();
	}

	/**
	 * Provide a modal's HTML content, overwrite if needed.
	 *
	 * By default a view is loaded from a file that's located in the
	 * modal_view_path, named $this->resource.php (where the dots have been replaced by underscores)
	 *
	 * You can return one of these options:
	 *  - Formo instance (just describe the fields, the rest will be taken care of)
	 *  - Mustache instance (requires a fully namespaced form)
	 *  - View instance (requires a fully namespaced form)
	 *
	 * The view data that gets passed contains:
	 *  - Resource
	 *  - Entity (same as the title)
	 *  - Model
	 *
	 * @return Formo|Mustache view|View
	 */
	public function modal(Array $data)
	{
		$path = Kohana::$config->load('table.modal_view_path').$data['resource'];

		return View::factory($path, $data);
	}

	/**
	 * Return typeAhead templates
	 *
	 * @param $sources string|array A string or array of sources from which you want the tpl.
	 * @return array
	 */
	public static function typeAhead_tpl($sources)
	{
		$data = ['type' => '', 'term' => '', 'handle' => 'tpl'];
		$response = [];

		foreach((array) $sources as $s)
		{
			$data['type'] = $s;
			$response[$s] = Plug::fire('admin.search', $data, true);
		}

		return $response;
	}

	public $actions = [];

} 