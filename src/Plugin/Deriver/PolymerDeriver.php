<?php

namespace Drupal\webcomponents\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ExtensionDiscovery;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\ui_patterns\Plugin\Deriver\YamlDeriver;
use Drupal\webcomponents\Plugin\UiPatterns\Pattern\PolymerPattern;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PolymerDeriver extends YamlDeriver implements ContainerDeriverInterface {

  /**
   * The regular expression for finding component files.
   *
   * @var string
   */
  protected $file_pattern = 'index.html';

  /**
   * Wrapper method for global function call.
   *
   * @see file.inc
   */
  public function fileScanDirectory($directory) {
    $options = ['nomask' => $this->getNoMask()];
    return file_scan_directory($directory, $this->file_pattern, $options, 0);
  }

  /**
   * Get list of definition files.
   *
   * Each entry contains:
   *  - provider: extension machine name providing the definition.
   *  - base path: base path of the definition file itself.
   *  - definitions: list definitions contained in the definition file.
   *
   * @return array
   *    List of definition files.
   */
  protected function getDefinitionFiles() {
    $files = [];
    foreach ($this->getDirectories() as $provider => $directory) {
      foreach ($this->fileScanDirectory($directory) as $pathname => $file) {
        $host_extension = $this->getHostExtension($pathname);
        if ($host_extension == FALSE || $host_extension == $provider) {
          $content = file_get_contents($pathname);
          $files[$pathname] = [
            'provider' => $provider,
            'base path' => dirname($pathname),
            'definitions' => PolymerPattern::decode($content),
          ];
        }
      }
    }

    return $files;
  }



}