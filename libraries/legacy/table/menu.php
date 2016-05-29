<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * Menu table
 *
 * @since  11.1
 */
class JTableMenu extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   11.1
	 */
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__menu', 'id', $db);

		// Set the default access level.
		$this->access = (int) JFactory::getConfig()->get('access');
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable::bind()
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		// Verify that the default home menu is not unset
		if ($this->home == '1' && $this->language == '*' && ($array['home'] == '0'))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_CANNOT_UNSET_DEFAULT_DEFAULT'));

			return false;
		}

		// Verify that the default home menu set to "all" languages" is not unset
		if ($this->home == '1' && $this->language == '*' && ($array['language'] != '*'))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_CANNOT_UNSET_DEFAULT'));

			return false;
		}

		// Verify that the default home menu is not unpublished
		if ($this->home == '1' && $this->language == '*' && $array['published'] != '1')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_UNPUBLISH_DEFAULT_HOME'));

			return false;
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success
	 *
	 * @see     JTable::check()
	 * @since   11.1
	 */
	public function check()
	{
		// Check for a title.
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_MENUITEM'));

			return false;
		}
		// Set correct component id to ensure proper 404 messages with separator items
		if ($this->type == "separator")
		{
			$this->component_id = 0;
		}

		// If the alias field is empty, set it to the title.
		$this->alias = trim($this->alias);

		if (empty($this->alias))
		{
			if ($this->type != 'alias' && $this->type != 'url')
			{
				$this->alias = $this->title;
			}
			elseif ($this->type == 'alias')
			{
				// If menu type alias test first if an alias already exists.
				$table      = JTable::getInstance('Menu', 'JTable', array('dbo' => $this->getDbo()));
				$testAlias  = JApplicationHelper::stringURLSafe($this->title);

				$itemSearch = array('alias' => $testAlias, 'parent_id' => $this->parent_id, 'client_id' => (int) $this->client_id);

				// Is a multilingual site.
				if (JLanguageMultilang::isEnabled())
				{
					$itemSearchAll                 = $itemSearch;
					$itemSearchAll['language']     = '*';
					$itemSearchCurrent             = $itemSearch;
					$itemSearchCurrent['language'] = $this->language;

					// If not exists a menu item at the same level with the same alias (in All language or in the same language).
					if (!$table->load($itemSearchAll) && !$table->load($itemSearchCurrent))
					{
						// Use the title as alias.
						$this->alias = $this->title;
					}
				}
				// Is a monolingual site.
				else
				{
					// If not exists a menu item at the same level with the same alias (in any language).
					if (!$table->load($itemSearch))
					{
						// Use the title as alias.
						$this->alias = $this->title;
					}
				}
			}
		}

		// Check for a path.
		if (trim($this->path) == '')
		{
			$this->path = $this->alias;
		}
		// Check for params.
		if (trim($this->params) == '')
		{
			$this->params = '{}';
		}
		// Check for img.
		if (trim($this->img) == '')
		{
			$this->img = ' ';
		}

		// Make the alias URL safe.
		$this->alias = JApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		// Cast the home property to an int for checking.
		$this->home = (int) $this->home;

		// Verify that a first level menu item alias is not 'component'.
		if ($this->parent_id == 1 && $this->alias == 'component')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_ROOT_ALIAS_COMPONENT'));

			return false;
		}

		// Verify that a first level menu item alias is not the name of a folder.
		jimport('joomla.filesystem.folder');

		if ($this->parent_id == 1 && in_array($this->alias, JFolder::folders(JPATH_ROOT)))
		{
			$this->setError(JText::sprintf('JLIB_DATABASE_ERROR_MENU_ROOT_ALIAS_FOLDER', $this->alias, $this->alias));

			return false;
		}

		// Verify that the home item a component.
		if ($this->home && $this->type != 'component')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_HOME_NOT_COMPONENT'));

			return false;
		}

		return true;
	}

	/**
	 * Overloaded store function
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  mixed  False on failure, positive integer on success.
	 *
	 * @see     JTable::store()
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$db = JFactory::getDbo();

		// Verify that the alias is unique
		$table = JTable::getInstance('Menu', 'JTable', array('dbo' => $this->getDbo()));

		// If the site is monolingual, we do not check for language
		if (!JLanguageMultilang::isEnabled())
		{
			if ($table->load(
					array(
					'alias' => $this->alias,
					'parent_id' => $this->parent_id,
					'client_id' => (int) $this->client_id
					)
				)
				&& ($table->id != $this->id || $this->id == 0))
			{

				$error = true;
			}
		}
		else
		{
			// If the site is multilingual, language ALL should be treated as when the site is monolingual
			if ($this->language == '*')
			{
				if ($table->load(
						array(
						'alias' => $this->alias,
						'parent_id' => $this->parent_id,
						'client_id' => (int) $this->client_id
						)
					)
					&& ($table->id != $this->id || $this->id == 0 || $table->language == '*' || $table->language != '*'))
				{

					$error = true;
				}
			}
			else
			{
				// If the site is multilingual, same alias is authorized when the language is different
				if ($table->load(
						array(
						'alias' => $this->alias,
						'parent_id' => $this->parent_id,
						'client_id' => (int) $this->client_id,
						'language' => $this->language
						)
					)
					&& ($table->id != $this->id || $this->id == 0))
				{

					$error = true;
				}
			}
		}

		if ($error)
		{
			if ($this->menutype == $table->menutype)
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_UNIQUE_ALIAS'));
			}
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_UNIQUE_ALIAS_ROOT'));
			}

			return false;
		}

		if ($this->home == '1')
		{
			// Verify that the home page for this menu is unique.
			if ($table->load(
					array(
					'menutype' => $this->menutype,
					'client_id' => (int) $this->client_id,
					'home' => '1'
					)
				)
				&& ($table->language != $this->language))
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_HOME_NOT_UNIQUE_IN_MENU'));

				return false;
			}

			// Verify that the home page for this language is unique
			if ($table->load(array('home' => '1', 'language' => $this->language)))
			{
				if ($table->checked_out && $table->checked_out != $this->checked_out)
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_DEFAULT_CHECKIN_USER_MISMATCH'));

					return false;
				}

				$table->home = 0;
				$table->checked_out = 0;
				$table->checked_out_time = $db->getNullDate();
				$table->store();
			}
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

		// Get the new path in case the node was moved
		$pathNodes = $this->getPath();
		$segments = array();

		foreach ($pathNodes as $node)
		{
			// Don't include root in path
			if ($node->alias != 'root')
			{
				$segments[] = $node->alias;
			}
		}

		$newPath = trim(implode('/', $segments), ' /\\');

		// Use new path for partial rebuild of table
		// Rebuild will return positive integer on success, false on failure
		return ($this->rebuild($this->{$this->_tbl_key}, $this->lft, $this->level, $newPath) > 0);
	}
}
