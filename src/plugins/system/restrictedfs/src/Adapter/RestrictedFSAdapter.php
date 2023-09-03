<?php
/**
 * The Restricted FS Adapter extends the Local filesystem adapter
 *
 * @copyright   (C) 2021 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Dgrammatiko\Plugin\System\RestrictedFS\Adapter;

\defined('_JEXEC') || die;

use Joomla\CMS\Uri\Uri;
use Joomla\Plugin\Filesystem\Local\Adapter\LocalAdapter;

/**
 * Restricted FS adapter.
 *
 * @since  1.0.0
 */
class RestrictedFSAdapter extends LocalAdapter
{
  /**
   * The file_path of media directory related to site
   *
   * @var string
   *
   * @since  1.0.0
   */
  private $filePath;
  private $adapterName;
  private $thumbnails;

  /**
   * The absolute root path in the local file system.
   *
   * @param   string  $rootPath  The root path
   * @param   string  $filePath  The file path of media folder
   *
   * @since   1.0.0
   */
  public function __construct(string $rootPath, string $filePath, string $adapterName, bool $thumbnails = false)
  {
    $this->filePath    = $filePath;
    $this->adapterName = $adapterName;
    $this->thumbnails  = $thumbnails;

    parent::__construct($rootPath, $filePath, $thumbnails, [200, 200]);
  }

  /**
   * Returns a url which can be used to display an image from within the "images/users" directory.
   *
   * @param   string  $path  Path of the file relative to adapter
   *
   * @return  string
   *
   * @since   1.0.0
   */
  public function getUrl(string $path): string
  {
    return Uri::root() . str_replace(" ", "%20", $this->adapterName . '/users/' . $this->filePath . $path);
  }
}
