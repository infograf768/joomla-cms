<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use  \Joomla\Utilities\ArrayHelper;

/**
 * Utility class working with content language select lists
 *
 * @since  1.6
 */
abstract class JHtmlContentLanguage
{
	/**
	 * Cached array of the content language items.
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected static $items = null;

	/**
	 * Cached array of content language assets.
	 *
	 * @var array
	 */
	protected static $assets = null;

	/**
	 * Get all language asset permissions
	 *
	 * @return array
	 */
	public static function assets()
	{
		if (is_null(static::$assets))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->select('name')
				->from($db->qn('#__assets'))
				->where($db->qn('name') . ' LIKE "com_languages.language.%"');

			$db->setQuery($query);
			static::$assets = $db->loadObjectList('name');
		}

		return static::$assets;
	}

	/**
	 * Get a list of all the possible available content language items.
	 *
	 * @return array
	 */
	public static function getItems()
	{
		if (empty(static::$items))
		{
			// Get the database object and a new query object.
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Build the query.
			$query->select('a.lang_code AS value, a.title AS text, a.title_native, a.lang_id AS lang_id')
				->select($query->concatenate(array('\'com_languages.language.\'', $db->qn('a.lang_id'))) . ' AS asset_name')
				->from('#__languages AS a')
				->where('a.published >= 0')
				->order('a.title');

			// Set the query and load the options.
			$db->setQuery($query);
			static::$items = $db->loadObjectList();
		}

		return static::$items;
	}

	/**
	 * Get a list of the available allowed content language items.
	 *
	 * @param   boolean $all       True to include All (*)
	 * @param   boolean $translate True to translate All
	 *
	 * @return  string
	 *
	 * @see     JFormFieldContentLanguage
	 * @since   1.6
	 */
	public static function existing($all = false, $translate = false)
	{
		$items    = self::getItems();
		$user     = JFactory::getUser();
		$assets = self::assets();

		foreach ($items as $key => $item)
		{
			$asset = ArrayHelper::getValue($assets, $item->asset_name);

			// Default to true for old content languages which have not had their acl saved.
			$canDoAssociations = !is_null($asset) ? $user->authorise('core.permission', $asset->name) : true;

			if (!$canDoAssociations)
			{
				unset (static::$items[$key]);
			}
		}

		if ($all)
		{
			$all_option = array((object) array('value' => '*', 'text' => $translate ? JText::alt('JALL', 'language') : 'JALL_LANGUAGE'));

			return array_merge($all_option, static::$items);
		}
		else
		{
			return static::$items;
		}
	}
}