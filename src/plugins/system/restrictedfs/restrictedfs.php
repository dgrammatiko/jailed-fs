<?php
/**
 * @copyright   (C) 2021 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later;
 */
defined('_JEXEC') || die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Media\Administrator\Event\MediaProviderEvent;
use Joomla\Component\Media\Administrator\Provider\ProviderInterface;

/**
 * Jailed FS plugin.
 */
class PlgSystemRestrictedfs extends CMSPlugin implements ProviderInterface
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
    if (count(array_intersect($this->app->getIdentity()->groups, (array) $this->params->get('jail_usergroups', []))) === 0) $this->jail = false;
    if (!$this->jail) return;

    // Disable all the filesystem adapters except this one
    $original = (new \ReflectionClass('\Joomla\CMS\Plugin\PluginHelper'))->getProperty('plugins');
    $original->setAccessible(true);
    $original->setValue(array_filter(
      $original->getValue(),
      function ($plugin) { if (isset($plugin->type) && $plugin->type !== 'filesystem') return false; }
    ));
  }

  /**
   * Patch the tinyMCE drag and drop adapter/path
   *
   * @return  void
   */
  public function onBeforeCompileHead(): void
  {
    $doc = $this->app->getDocument();

    if ($doc->getType() !== 'html') {
      return;
    }

    $data = $doc->getHeadData();
    if (
      !isset($data['scriptOptions']['plg_editor_tinymce'])
      || !isset($data['scriptOptions']['plg_editor_tinymce']['tinyMCE'])
      || count(array_intersect($this->app->getIdentity()->groups, (array) $this->params->get('jail_usergroups', []))) === 0) {
      return;
    }

    $options = $data['scriptOptions']['plg_editor_tinymce']['tinyMCE'];
    if (!is_array($options) || count($options) === 0 || !isset($options['default'])) {
      return;
    }

    $tinyMCE = new stdClass;
    $tinyMCE->tinyMCE = [];
    $tinyMCE->tinyMCE['default'] = $options['default'];
    if (isset($options['default']['comMediaAdapter'])) {
      $options['default']['comMediaAdapter'] = 'restrictedfs-' . $this->app->getIdentity()->username . ':';
      $options['default']['parentUploadFolder'] = '';
    }

    // Reassign the options
    foreach ($options as $key => $value) {
      $tinyMCE->tinyMCE[$key] = $value;
    }

    $doc->addScriptOptions('plg_editor_tinymce', $tinyMCE, true);
  }

  /**
   * Setup Providers for Jailed Adapter
   */
  public function onSetupProviders(MediaProviderEvent $event): void
  {
    // Don't register this provider if we're not jailed
    if (!$this->jail) return;
    $event->getProviderManager()->registerProvider($this);
  }

  /**
   * Returns the ID of the provider
   */
  public function getID(): string
  {
    return $this->_name;
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
  public function getAdapters()
  {
    $userName = $this->app->getIdentity()->username;
    $directoryPath = JPATH_ROOT . '/images/users/' . md5($userName);
    if (!is_dir($directoryPath)) mkdir($directoryPath, 0777, true);

    $adapter = new \Joomla\Plugin\System\RestrictedFS\Adapter\RestrictedFSAdapter(
      $directoryPath . '/',
      $userName
    );

    return [$adapter->getAdapterName() => $adapter];
  }
}
