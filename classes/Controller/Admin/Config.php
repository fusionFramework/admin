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
class Controller_Admin_Config extends Controller_Fusion_Admin {

	protected function _load_def($file)
	{
		$load = Kohana::find_file('module'.DIRECTORY_SEPARATOR.'cfg', $file);

		if($load != false)
		{
			return Kohana::load($load);
		}
		else
			throw new HTTP_Exception_404;
	}
	public function action_index()
	{
		Fusion::$assets->add_set('modals');
		Fusion::$assets->add_set('uploadify');
		Fusion::$assets->add_set('req');
		Fusion::$assets->add_js('admin/config.js');

		$this->_tpl = new View_Admin_Config;
		$this->_tpl->definitions = Kohana::list_files('module'.DIRECTORY_SEPARATOR.'cfg');
	}

	public function action_load()
	{
		$file = $this->request->param('file');
		$this->access('admin.config.'.$file);

		$def = $this->_load_def($file);
			$forms = [];
			$first = false;
			foreach($def['formo'] as $ind => $form)
			{
				if($first == false)
				{
					$first = $ind;
				}

				$forms[$ind] = Formo::form(['alias' => $form['alias']]);

				if(isset($form['legend']))
				{
					$forms[$ind]->set('legend', $form['legend']);
				}

				if(isset($form['fields']))
				{
					$cfg = (isset($form['as_sub_to'])) ? $form['as_sub_to'].'.'.$form['alias'] : $form['alias'];

					//add fields & values
					foreach($form['fields'] as $field)
					{
						$field_cfg_key = $cfg.'.'.$field[0];
						$field[2] = Kohana::$config->load($field_cfg_key);
						$forms[$ind]->add($field);
					}

					//if it's a subform add it to its parent
					if(isset($form['as_sub_to']))
					{
						$forms[$form['as_sub_to']]->add($forms[$ind]);
					}
				}
			}

			$img = false;
			if(isset($def['image']))
			{
				$img = [];

				foreach($def['image'] as $name => $d)
				{
					$img[] = [
						'input' => $d['input'],
						'swf' => URL::site('assets/uploadify.swf'),
						'save' => Route::url('admin.config.upload', ['file' => $file, 'image' => $name], true),
						'formData' => ['csrf' => Security::token()],
						'fileObjName' => $name,
						'fileTypeExts' => '*.png',
						'fileTypeDesc' => 'Image'
					];
				}

			}

			RD::set(RD::SUCCESS, 'Config form load successful', null, [
				'form' => $forms[$first]->render('bootstrap/form_template'),
				'img' => $img
			]);
	}

	public function action_upload()
	{
		$file = $this->request->param('file');
		$image = $this->request->param('image');

		$def = $this->_load_def($file);

		if(!isset($def['images'][$image]))
			throw new HTTP_Exception_404;

		$resource_dir = DOCROOT.'media'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$image;

		if(!file_exists($resource_dir))
		{
			mkdir($resource_dir);
		}

		$file_name = Text::random() . '.png';
		$return = Upload::save($_FILES[$image], $file_name, $resource_dir);

		if($return != false)
		{
			$url = Route::url('admin.tmp', array('file' => 'admin'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$image.DIRECTORY_SEPARATOR.$file_name), true);
			$this->response->body($file_name.'*/*'.$url);
		}
		else
		{
			throw new Kohana_HTTP_Exception_400;
		}
	}

	public function action_save()
	{
		$file = $this->request->param('file');

		$def = $this->_load_def($file);

		try {
			$config = Kohana::$config->load($file);
			$values = Arr::paths($_POST);

			if(isset($def['images']))
			{
				foreach($def['images'] as $image)
				{
					if($values[$image['path']] != '')
					{
						$tmp_file = DOCROOT.'media'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$values[$image['path']];
						copy($tmp_file, $image['save_to']);
						unlink($tmp_file);
						$values[$image['path']] = basename($image['save_to']);
					}
				}
			}

			$cfg_name_length = strlen($file) + 1;
			foreach($values as $path => $val)
			{
				Arr::set_path($config, substr($path, $cfg_name_length), $val);
			}

			$config->export(APPPATH.'config');

			RD::set(RD::SUCCESS, ':config config file updated successfully', [':config' => $file]);
		}
		catch(Kohana_Exception $e)
		{
			RD::set(RD::ERROR, $e->getMessage());
		}
	}

} // End Config
