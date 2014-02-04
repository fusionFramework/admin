<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Button group item template for Twitter Bootstrap.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Maxim kerstens <admin@happydemon.org>
 * @package hD/elements
 * @copyright (c) 2013, happyDemon
 */
?>
<div class="btn-group">
	<?foreach ($menu->get_visible_items() as $item): ?>
		<?=$item->render(true);?>
	<? endforeach?>
</div>