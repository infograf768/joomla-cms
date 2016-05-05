<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var  string    $clientId        The Client id (site/admin)
 * @var  string    $inputTag        The input field
 */
extract($displayData);

// Add the script to the document head.
JFactory::getDocument()->addScriptDeclaration(
	'
	function jSelectPosition_' . $id . '(name) {
		document.getElementById("' . $id . '").value = name;
		jQuery("#module' . $id . 'PositionModal").modal("hide");
	}
	'
);

$link = 'index.php?option=com_modules&view=positions&layout=modal&tmpl=component&function=jSelectPosition_'
	. $id . '&amp;client_id=' . $clientId;

echo JHtml::_(
	'bootstrap.renderModal',
	'module' . $id . 'PositionModal',
	array(
		'url' => $link,
		'title' => JText::_('COM_MODULES_CHANGE_POSITION_TITLE'),
		'height' => '300px',
		'width' => '800px'
	)
);
?>
<div class="input-append">
	<?php echo $inputTag; ?>
	<button onclick="jQuery('#module<?php echo $id; ?>PositionModal').modal('show')" class="btn" data-toggle="modal" title="<?php echo JText::_('COM_MODULES_CHANGE_POSITION_BUTTON'); ?>">
		<span><?php echo JText::_('COM_MODULES_CHANGE_POSITION_BUTTON'); ?></span>
	</button>
</div>