<?php

/**
 * @copyright   (C) 2021 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later;
 */

namespace Dgrammatiko\Plugin\System\RestrictedFS\Extension;

defined('_JEXEC') || die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Media\Administrator\Event\MediaProviderEvent;
use Joomla\Component\Media\Administrator\Provider\ProviderInterface;
use Joomla\Event\SubscriberInterface;

/**
 * Jailed FS plugin.
 */
final class RestrictedFS extends CMSPlugin implements ProviderInterface, SubscriberInterface
{
  /**
   * Should the user be jailed?
   *
   * @var  boolean
   */
  protected $jail = true;

  /**
   * Should the username be masked?
   *
   * @var  boolean
   */
  protected $masked = false;

  public function __construct(&$subject, $config = [])
  {
    parent::__construct($subject, $config);

    $this->masked = (bool) $this->params->get('mask_usernames', 0);
  }

  /**
   * Returns an array of CMS events this plugin will listen to and the respective handlers.
   *
   * @return  array
   */
  public static function getSubscribedEvents(): array
  {
    return [
      'onAfterRoute'        => 'onAfterRoute',
      'onBeforeCompileHead' => 'onBeforeCompileHead',
      'onSetupProviders'    => 'onSetupProviders',
    ];
  }

  /**
   * @return  void
   */
  public function onSetupProviders(MediaProviderEvent $event): void
  {
    // Register our provider
    $event->getProviderManager()->registerProvider($this);
  }

  /**
   * @return  void
   */
  public function onAfterRoute(): void
  {
    $app = $this->getApplication();
    // Bail out early
    if (!$app || $app->input->get('option') !== 'com_media') return;
    $PluginUserGroups = (array) $this->params->get('jail_usergroups', []);
    $userGroups       = $app->getIdentity()->groups;
    if (count(array_intersect($userGroups, $PluginUserGroups)) === 0) return;

    // Remove local filesystem from PluginHelper's loaded plugins
    try {
      $pluginsProp = new \ReflectionProperty(\Joomla\CMS\Plugin\PluginHelper::class, 'plugins');
      $plugins = $pluginsProp->getValue();
      $filtered = [];
      foreach ($plugins as $p) {
        if ($p->type === 'filesystem' && $p->name === 'local') {
          continue;
        }
        $filtered[] = $p;
      }
      $pluginsProp->setValue(null, array_values($filtered));
    } catch (\Throwable $e) {
      // Ignore reflection errors
    }

    // Import the plugin to trigger onSetupProviders
    \Joomla\CMS\Plugin\PluginHelper::importPlugin('system', 'restrictedfs');

    // Import filesystem - the plugin will now register itself
    \Joomla\CMS\Plugin\PluginHelper::importPlugin('filesystem');
  }

  /**
   * Patch the tinyMCE drag and drop adapter/path
   *
   * @return  void
   */
  public function onBeforeCompileHead(): void
  {
    $app = $this->getApplication();
    $doc = $app->getDocument();

    if ($doc->getType() !== 'html') return;

    $data = $doc->getHeadData();
    if (
      !isset($data['scriptOptions']['plg_editor_tinymce'])
      || !isset($data['scriptOptions']['plg_editor_tinymce']['tinyMCE'])
      || count(array_intersect($app->getIdentity()->groups, (array) $this->params->get('jail_usergroups', []))) === 0
    ) return;

    $options = $data['scriptOptions']['plg_editor_tinymce']['tinyMCE'];
    if (!is_array($options) || count($options) === 0 || !isset($options['default'])) return;

    $user = $this->getApplication()->getIdentity();
    if ($this->masked) {
      $userName = md5($user->username);
    } else {
      $userName = urlencode(str_replace(['@', '.', '\\', '/', '*', '?', '<', '>'], ['_', '-', '_', '_', '_', '_', '_', '_'], $user->username));
    }
    $tinyMCE = (object) ['tinyMCE' => ['default' => $options['default']]];
    if (isset($options['default']['comMediaAdapter'])) {
      $options['default']['comMediaAdapter'] = 'restrictedfs-' . $userName . ':';
      $options['default']['parentUploadFolder'] = '';
    }

    // Reassign the options
    foreach ($options as $key => $value) {
      $tinyMCE->tinyMCE[$key] = $value;
    }

    $doc->addScriptOptions('plg_editor_tinymce', $tinyMCE, true);
  }

  /**
   * Returns the ID of the provider
   */
  public function getID(): string
  {
    return 'restrictedfs';
  }

  /**
   * Returns the display name of the provider
   */
  public function getDisplayName(): string
  {
    return 'Restricted FS';
  }

  /**
   * Returns and array of adapters
   *
   * @return  \Joomla\Component\Media\Administrator\Adapter\AdapterInterface[]
   */
  public function getAdapters(): array
  {
    $app = $this->getApplication();
    if (!$app) return [];

    $user = $app->getIdentity();
    $storagePath = $this->params->get('storage_path', 'images');
    $userName = $this->masked
      ? md5($user->username)
      : urlencode(str_replace(['@', '.', '\\', '/', '*', '?', '<', '>'], ['_', '-', '_', '_', '_', '_', '_', '_'], $user->username));

    $directoryPath = JPATH_ROOT . '/' . $storagePath . '/users/' . $userName;
    if (!is_dir($directoryPath)) mkdir($directoryPath, 0755, true);

    $adapter = new \Dgrammatiko\Plugin\System\RestrictedFS\Adapter\RestrictedFSAdapter(
      $directoryPath . '/',
      $userName,
      $storagePath,
      (bool) $this->params->get('thumbs', false)
    );

    return [$adapter->getAdapterName() => $adapter];
  }
}
