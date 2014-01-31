<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Navigation item template for Twitter Bootstrap main navbar.
 * Render the output inside div.navbar>div.navbar-inner>.container
 *
 * @link http://twitter.github.com/bootstrap/components.html#navbar
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 2.0
 * @package hD/elements
 * @copyright (c) 2012, Ando Roots
 */
?>
<ul id="menu" class="collapse">
	<li class="nav-header">Menu</li>
	<li class="nav-divider"></li>

	<?foreach ($menu->get_visible_items() as $item):

	// Is this a dropdown-menu with sibling links?
	if ($item->has_siblings()):?>

		<li class="panel  <?=$item->get_classes()?>">
			<a href="<?=$item->url?>" data-parent="#menu" title="<?=$item->tooltip?>" data-toggle="collapse" class="<?=($item->url == "#" && $item->route == '') ? 'nolink ' : ''?>accordion-toggle" data-target="#side-name-<?=str_replace(' ', '', $item->title)?>">
				<i class="<?=$item->icon;?>"></i> <?=$item->title?>
	            <span class="pull-right nolink">
					<i class="fa fa-angle-left"></i>
                </span>
			</a>
			<ul class="collapse" id="side-name-<?=str_replace(' ', '', $item->title)?>">
				<?foreach ($item->siblings as $subitem): ?>
					<li class="<?=$subitem->get_classes()?>">
						<?=(string) $subitem?>
					</li>
				<? endforeach?>
			</ul>
		</li>

		<? else:
		// No, this is a "normal", single-level menu
		?>
		<li class="<?=$item->get_classes()?>">
			<?=(string) $item?>
		</li>

		<? endif ?>

	<? endforeach?>
</ul>