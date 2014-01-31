<?php
defined( 'SYSPATH' ) or die( 'No direct script access.' );
/**
* Admin dataTable base extension for fusionFramework
*
* @author Maxim Kerstens
* @package admin
*/
class Table extends Kohana_Table
{
	/**
	 * Buttons that are put at the end of every table row
	 * @var array
	 */
	protected $_row_buttons = array(
		'edit' => array(
			'icon' => 'fa-edit',
			'class' => 'warning',
			'title' => 'Edit'
		),
		'remove' => array(
			'icon' => 'fa-times-circle',
			'class' => 'danger',
			'title' => 'Delete'
		)
	);

	public function js_actions($options)
	{
		$resource = $options['resource'];
		$modal_definition = $options['modal'];
		$upload_url = ($options['images'] == true) ? Route::url('admin.'.str_replace('_', '.', $resource).'.upload') : '';
		return View::factory('datatable/actions', array(
			'id' => Inflector::underscore($this->_name),
			'resource' => $resource,
			'history' => ($options['history'] != false),
			'images' => $options['images'],
			'upload_url' => $upload_url,
			'uploadify' => URL::site('assets/uploadify.swf', true),
			'entity' => Inflector::humanize($resource),
			'csrf' => Security::token(),
			'url_remove' => Route::url('admin.'.str_replace('_', '.', $resource).'.remove', array('id' => 0), true),
			'url_load' => Route::url('admin.'.str_replace('_', '.', $resource).'.modal', array('id' => 0), true),
			'url_save' => Route::url('admin.'.str_replace('_', '.', $resource).'.save', null, true),
			'datatable' => 'data'.$this->_name,
			'modal' => json_encode($modal_definition, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT)
		));
	}

	/**
	 * Generate a basic html table and cache it
	 *
	 * @return string HTML table parsed template
	 */
	public function template_table($id) {
		$cache_view = 'happyDemon.table.'.$this->_name;

		if($id != null)
		{
			$cache_view .= '.'.$id;
		}

		$cache = Cache::instance(Kohana::$config->load('notePad-grds.cache_group'));

		if (!$view = $cache->get($cache_view.'.tpl', FALSE))
		{
			$this->_options();
			$heads = array();

			foreach($this->_columns as $name) {
				$heads[] = array(
					'class' => $this->_column_definitions[$name]['class'],
					'title' => $this->_column_definitions[$name]['head']
				);
			}

			$view = View::factory('notePad/table', array(
				'heads' => $heads,
				'head_count' => count($heads),
				'id' => Inflector::underscore($this->_name),
				'data' => $id,
				'title' => $this->_name,
				'class' => $this->_config['class_table']
			));

			$cache->set($cache_view.'.tpl', $view, Kohana::$config->load('notePad-grds.cache_lifetime'));
		}

		return $view;
	}
}