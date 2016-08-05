<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
}

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.core');
JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
JHtml::_('formbehavior.chosen', 'select');

// Special case for the search field tooltip.
$searchFilterDesc = $this->filterForm->getFieldAttribute('search', 'description', null, 'filter');
JHtml::_('bootstrap.tooltip', '#filter_search', array('title' => JText::_($searchFilterDesc), 'placement' => 'bottom'));

$function     = $app->input->get('function', 'jSelectMenuItem', 'cmd');
$listOrder    = $this->escape($this->state->get('list.ordering'));
$listDirn     = $this->escape($this->state->get('list.direction'));

$app->getDocument()->addScriptDeclaration("
jQuery(document).ready(function($) {
	$('body').on('click', '.select-link', function() {
		// Run function on parent window.
		if(self != top)
		{
			window.parent." . $function . "(this.getAttribute('data-id'), this.getAttribute('data-title'), null, null, this.getAttribute('data-uri'), this.getAttribute('data-language'), null);
		}
	});
});");
?>
<div class="container-popup">

	<form action="<?php echo JRoute::_('index.php?option=com_menus&view=items&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">

		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th class="title">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MENUS_HEADING_MENU', 'menutype_title', $listDirn, $listOrder); ?>
						</th>
						<th width="5%" class="center nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MENUS_HEADING_HOME', 'a.home', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="7">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<?php if ($item->type != 'separator' && $item->type != 'alias' && $item->type != 'heading' && $item->type != 'url') : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
								<?php echo JHtml::_('MenusHtml.Menus.state', $item->published, $i, 0); ?>
							</td>
							<td>
								<?php $prefix = JLayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level)); ?>
								<?php echo $prefix; ?>
								<a class="select-link" href="javascript:void(0)"
									data-funcion="<?php echo $this->escape($function); ?>"
									data-id="<?php echo $item->id; ?>"
									data-title="<?php echo $this->escape(addslashes($item->title)); ?>"
									data-uri="<?php echo $this->escape(JRoute::_('index.php?Itemid=' . $item->id)); ?>"
									data-language="<?php echo $this->escape($item->language); ?>">
									<?php echo $this->escape($item->title); ?></a>
								<span class="small">
									<?php if (empty($item->note)) : ?>
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
									<?php else : ?>
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
									<?php endif; ?>
								</span>
								<div title="<?php echo $this->escape($item->path); ?>">
									<?php echo $prefix; ?>
									<span class="small" title="<?php echo isset($item->item_type_desc) ? htmlspecialchars($this->escape($item->item_type_desc), ENT_COMPAT, 'UTF-8') : ''; ?>">
										<?php echo $this->escape($item->item_type); ?></span>
								</div>
							</td>
							<td class="small hidden-phone">
								<?php echo $this->escape($item->menutype_title); ?>
							</td>
							<td class="center hidden-phone">
								<?php if ($item->type == 'component') : ?>
									<?php if ($item->language == '*' || $item->home == '0') : ?>
										<?php echo JHtml::_('jgrid.isdefault', $item->home, $i, 'items.', ($item->language != '*' || !$item->home) && 0); ?>
									<?php else : ?>
										<?php echo JHtml::_('image', 'mod_languages/' . $item->language_image . '.gif', $item->language_title, array('title' => $item->language_title), true); ?>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td class="small hidden-phone">
								<?php echo $this->escape($item->access_level); ?>
							</td>
							<td class="small hidden-phone">
								<?php if ($item->language == ''):?>
									<?php echo JText::_('JDEFAULT'); ?>
								<?php elseif ($item->language == '*') : ?>
									<?php echo JText::alt('JALL', 'language'); ?>
								<?php else : ?>
									<?php echo $item->language_title ? JHtml::_('image', 'mod_languages/' . $item->language_image . '.gif', $item->language_title, array('title' => $item->language_title), true) . '&nbsp;' . $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
								<?php endif; ?>
							</td>
							<td class="hidden-phone">
								<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
									<?php echo (int) $item->id; ?>
								</span>
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="function" value="<?php echo $function; ?>" />
		<input type="hidden" name="forcedLanguage" value="<?php echo $app->input->get('forcedLanguage', '', 'cmd'); ?>" />
		<?php echo JHtml::_('form.token'); ?>

	</form>
</div>
