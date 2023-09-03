<?php

/**
 * @copyright   (C) 2023 Dimitrios Grammatikogiannis
 * @license     GNU General Public License version 2 or later;
 */

namespace Dgrammatiko\Plugin\System\RestrictedFS\Field;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Generates the list of directories  available for user restricted uploads.
 */
class StoragePathField extends ListField
{
    protected $type = 'storagepath';

    /**
     * Method to get the directories options.
     */
    public function getOptions(): array
    {
      $plugin = PluginHelper::getPlugin('filesystem', 'local');
      if ($plugin) {
        $directories = (new Registry($plugin->params))->get('directories', '[{"directory": "images"}]');
        $options     = [];

        // Do a check if default settings are not saved by user, if not initialize them manually
        if (is_string($directories)) {
            $directories = json_decode($directories);
        }

        // Default to images
        $selected = $this->value ? $this->value : 'images';

        foreach ($directories as $ind => $directoryEntity) {
          $options[] = (object) [
            'value'    => $directoryEntity->directory,
            'text'     => $directoryEntity->directory,
            'selected' => $selected === $directoryEntity->directory,
          ];
        }

        return $options;
      }

      return [(object) [
        'value'    => 'images',
        'text'     => 'images',
        'selected' => true,
      ]];
    }

    /**
     * Method to get the field input markup for the list of directories.
     */
    protected function getInput(): string
    {
      // Get the field options.
      $options = (array) $this->getOptions();

      return HTMLHelper::_('select.genericlist', $options, $this->name, 'class="form-select"', 'value', 'text', $this->value, $this->id);
    }
}
