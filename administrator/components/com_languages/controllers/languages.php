<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Languages controller Class.
 *
 * @since  1.6
 */
class LanguagesControllerLanguages extends JControllerAdmin
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Language', $prefix = 'LanguagesModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public function saveOrderAjax()
	{
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model.
		$model = $this->getModel();

		// Save the ordering.
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application.
		JFactory::getApplication()->close();
	}

	/**
	 * Remove an item.
	 *
	 * @return  void
	 *
	 * @since   3.6.0
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();
		$app  = JFactory::getApplication();
		$cids = (array) $this->input->get('cid', array(), 'array');

		if (count($cids) < 1)
		{
			$app->enqueueMessage(JText::_('COM_LANGUAGES_NO_LANGUAGE_SELECTED'), 'notice');
		}
		else
		{
			// Access checks.
			foreach ($cids as $i => $id)
			{
				if (!$user->authorise('core.delete', 'com_languages.language.' . (int) $id))
				{
					// Prune items that you can't change.
					unset($cids[$i]);
					$app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 'error');
				}
			}

			if (count($cids) > 0)
			{
				// Get the model.
				$model = $this->getModel();

				// Make sure the item ids are integers
				$cids = ArrayHelper::toInteger($cids);

				// Remove the items.
				if (!$model->delete($cids))
				{
					$this->setMessage($model->getError());
				}
				else
				{
					$this->setMessage(JText::plural('COM_LANGUAGES_N_LANGUAGES_DELETED', count($cids)));
				}
			}
		}
		$this->setRedirect('index.php?option=com_languages&view=languages');
	}
}
