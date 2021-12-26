<?php

/**
 * @copyright   (C) 2021 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Media\Administrator\Event\MediaProviderEvent;
use Joomla\Component\Media\Administrator\Provider\ProviderInterface;

/**
 * Jailed FS plugin.
 */
class PlgSystemJailed extends CMSPlugin implements ProviderInterface
{
  /**
   * Application object.
   *
   * @var  \Joomla\CMS\Application\CMSApplication
   */
  protected $app;

  /**
   * Should the user be jailed?
   *
   * @var  boolean
   */
  protected $jail = true;

  /**
   * @return  void
   */
  public function onAfterRoute(): void
  {
    // Bail out early
    if ($this->app->input->get('option') !== 'com_media') return;
    // Bail out early
    if (!in_array((int) $this->params->get('jail_usergroups', 2), $this->app->getIdentity()->groups)) {
      $this->jail = false;
      return;
    }

    $reflectionClass = new ReflectionClass('\Joomla\CMS\Plugin\PluginHelper');
    $original = $reflectionClass->getProperty('plugins');
    $original->setAccessible(true);
    $cachedValue = $original->getValue();
    $newRegistry = [];
    foreach ($cachedValue as $plugin) {
      if ($plugin->type !== 'filesystem') {
        $newRegistry[] = $plugin;
      }
    }

    $original->setValue($newRegistry);
  }

  /**
   * Setup Providers for Jailed Adapter
   *
   * @param   MediaProviderEvent  $event  Event for ProviderManager
   *
   * @return   void
   */
  public function onSetupProviders(MediaProviderEvent $event)
  {
    if (!$this->jail) return;
    $event->getProviderManager()->registerProvider($this);
  }

  /**
   * Returns the ID of the provider
   *
   * @return  string
   */
  public function getID()
  {
    return $this->_name;
  }

  /**
   * Returns the display name of the provider
   *
   * @return string
   */
  public function getDisplayName()
  {
    return 'Restricted FS'; //Text::_('PLG_FILESYSTEM_JAILED_DEFAULT_NAME');
  }

  /**
   * Returns and array of adapters
   *
   * @return  \Joomla\Component\Media\Administrator\Adapter\AdapterInterface[]
   */
  public function getAdapters()
  {
    $user = Factory::getUser();
    $userName = str_replace(' ', '_', $user->username);

    $directoryPath = JPATH_ROOT . '/images/users/' . $userName;

    if (!is_dir($directoryPath)) {
      mkdir($directoryPath, 0777, true);
    }

    $adapter = new \Joomla\Plugin\System\Jailed\Adapter\JailedAdapter(
      $directoryPath . '/',
      $userName
    );

    $name = $adapter->getAdapterName();

    return [$name => $adapter];
  }
}
