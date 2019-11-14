<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Extension\Service\Provider;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Dispatcher\ModuleDispatcherFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Service provider for the service dispatcher factory.
 *
 * @since  4.0.0
 */
class ModuleDispatcherFactory implements ServiceProviderInterface
{
	/**
	 * The module namespace
	 *
	 * @var  string
	 *
	 * @since   4.0.0
	 */
	private $namespace;

	/**
	 * ComponentDispatcherFactory constructor.
	 *
	 * @param   string  $namespace  The namespace
	 *
	 * @since   4.0.0
	 */
	public function __construct(string $namespace)
	{
		$this->namespace = $namespace;
	}

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container)
	{
		$container->set(
			ModuleDispatcherFactoryInterface::class,
			function (Container $container)
			{
				return new \Joomla\CMS\Dispatcher\ModuleDispatcherFactory($this->namespace);
			}
		);
	}
}
