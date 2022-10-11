<?php
/**
 * @copyright   (C) 2022 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\RestrictedFS\Extension\RestrictedFS;

return new class implements ServiceProviderInterface
{
  public function register(Container $container)
  {
    $container->set(
      PluginInterface::class,
      function (Container $container)
      {
        $plugin                 = PluginHelper::getPlugin('system', 'restrictedfs');
        $dispatcher             = $container->get(DispatcherInterface::class);
        $documentFactory        = $container->get('document.factory');

        $plugin = new RestrictedFS($dispatcher, (array) $plugin, $documentFactory);
        $plugin->setApplication(Factory::getApplication());

        return $plugin;
      }
    );
  }
};
