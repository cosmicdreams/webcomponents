<?php

<?php

namespace Drupal\webcomponents\Plugin\UiPatterns\Pattern;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\ui_patterns\UiPatternBase;
use Drupal\ui_patterns\UiPatternInterface;

/**
 * The UI Pattern plugin.
 *
 * @UiPattern(
 *   id = "polymer",
 *   label = @Translation("Polymer Pattern"),
 *   description = @Translation("Pattern extracted from polymer components."),
 *   deriver = "\Drupal\webcomponents\Plugin\Deriver\PolymerDeriver"
 * )
 */
class PolymerPattern extends UiPatternBase implements UiPatternInterface {

  /**
   * Extract schema and structure from a web component
   *
   * Each component contains:
   *  - machine_name: the internal name for the component.
   *  - tag: the custom tag that has been defined for the component.
   *  - title: human readable name for the component.
   *  - file: the file location for the component.
   *  - properties: any extra properties that have been defined by the component.
   *
   * @return \stdClass
   *    List of definition files.
   */
  public function decode($raw) {

    /** @var \DOMDocument $html_obj */
    $html_obj = Html::load($raw);
    // find dom-module, it's the machine_name
    /** @var \DOMElement $dom_module */
    $dom_module = $html_obj->getElementsByTagName('dom-module')->item(0);
    $machine_name = $dom_module->getAttribute('id');
    $title = Unicode::ucfirst(str_replace('-', ' ', $machine_name));

    // establish the component we found
    /** @var \DOMElement $template */
    $template = $html_obj->getElementsByTagName('template')->item(0);
    $tag = $template->tagName;

    $component = new stdClass();
    $component->machine_name = $machine_name;
    $component->tag = $tag;
    $component->title = $title;
    $component->properties = array();
    // match {{}} and [[]] based properties
    preg_match_all('/({{.*?}}|\[\[.*?\]\])/', $rawmatch->innertext, $matches);
    // find matches for custom properties so we know how to tokenize this
    if (isset($matches[0])) {
      // loop through properties
      foreach ($matches[0] as $match) {
        // strip off the edges
        $value = str_replace('{{', '', str_replace('}}', '', $match));
        $value = str_replace('[[', '', str_replace(']]', '', $value));
        // @todo read value type from polymer directly
        $component->properties[$value] = 'String';
      }
    }

    // Release resources to avoid memory leak in some versions.
    unset($html_obj);
    return $component;
  }

  private function _polymer_pattern_dom_innertext($html) {

  }
}
