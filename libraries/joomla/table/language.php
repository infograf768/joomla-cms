<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Languages table.
 *
 * @since  11.1
 */
class JTableLanguage extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   11.1
	 */
	public function __construct($db)
	{
		parent::__construct('#__languages', 'lang_id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_LANGUAGE_NO_TITLE'));

			return false;
		}

		return true;
	}

	/**
	 * Overrides JTable::store to check unique fields.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.4
	 */
	public function store($updateNulls = false)
	{
		// Verify that the sef field is unique
		$table = JTable::getInstance('Language', 'JTable', array('dbo' => $this->getDbo()));

		if ($table->load(array('sef' => $this->sef)) && ($table->lang_id != $this->lang_id || $this->lang_id == 0))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_LANGUAGE_UNIQUE_SEF'));

			return false;
		}

		// Verify that the image field is unique
		if ($table->load(array('image' => $this->image)) && ($table->lang_id != $this->lang_id || $this->lang_id == 0))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_LANGUAGE_UNIQUE_IMAGE'));

			return false;
		}

		// Verify that the language code is unique
		if ($table->load(array('lang_code' => $this->lang_code)) && ($table->lang_id != $this->lang_id || $this->lang_id == 0))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_LANGUAGE_UNIQUE_LANG_CODE'));

			return false;
		}

		return parent::store($updateNulls);
	}

	/**
	 * Overloaded bind function.
	 *
	 * @param   array  $array   Named array.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable::bind()
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		return 'com_languages.language.' . (int) $this->lang_id;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID,
	 * which will default to 1 if none exists.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetId = null;
		$asset = JTable::getInstance('asset');
		if ($asset->loadByName('com_languages'))
		{
			$assetId = $asset->id;
		}
		return is_null($assetId) ? parent::_getAssetParentId($table, $id) : $assetId;
	}
}
