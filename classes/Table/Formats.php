<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Javascript defined functions for rendering dataTable columns.
 * 
 * @author happydemon
 * @package happyDemon/notePad
 */
class Table_Formats extends Kohana_Table_Formats {
	/**
	 * Parse the option buttons for at the end of the dataTable
	 *
	 * @param mixed $param
	 * @return string
	 */
	public static function options($param=null) {
		$return = "return '";

		foreach($param as $id => $def) {
			$class = (isset($def['class'])) ? ' btn-'.$def['class'] : '';

			$return .= ' <button data-id="\'+data+\'"';

			if(isset($def['title']))
			{
				$return .= ' title="'.$def['title'].'"';
			}

			$return .=' class="btn'.$class.' btn-sm btn-action-'.$id.'"><i class="fa '.$def['icon'].'"></i></button>';
		}

		return $return."';\n";
	}
}