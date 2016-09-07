<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
JLoader::register('ContentHelperAssociation', JPATH_SITE . '/components/com_content/helpers/association.php');
$id = $displayData['item']->id;
?>
<?php if (ContentHelperAssociation::displayAssociations($id) != null) : ?>
<dd class="association">
	<?php echo JText::_('JASSOCIATIONS'); ?>
	<?php echo ContentHelperAssociation::displayAssociations($id); ?>
</dd>
<?php endif; ?>
