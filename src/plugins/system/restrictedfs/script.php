<?php
/**
 * @copyright   (C) 2021 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later;
 */
defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\PluginAdapter;
use Joomla\CMS\Installer\InstallerScript;

class plgSystemRestrictedfsInstallerScript extends InstallerScript
{
  public function postflight($type, PluginAdapter $parent)
  {
    // Enable the plugin
    if ($type === 'install' || $type === 'discover_install') {
      $db = Factory::getDbo();
      $query = $db->getQuery(true)
        ->update('#__extensions')
        ->set($db->qn('enabled') . ' = 1')
        ->where($db->qn('type') . ' = ' . $db->q('plugin'))
        ->where($db->qn('element') . ' = ' . $db->q('restrictedfs'))
        ->where($db->qn('folder') . ' = ' . $db->q('system'));
      $db->setQuery($query);
      try {
        $db->execute();
      } catch (\Exception $e) {
        // var_dump($e);
      }
    }
  }
}
