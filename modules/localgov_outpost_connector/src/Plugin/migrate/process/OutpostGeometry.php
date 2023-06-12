<?php

namespace Drupal\localgov_outpost_connector\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides an outpost_geometry plugin.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: outpost_geometry
 *     source: geometry
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "outpost_geometry",
 *   handle_multiples = TRUE
 * )
 */
class OutpostGeometry extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // This is a _guess_ what the format of the data would be if it's not a
    // point. It will work with a point.
    if (is_array($value)) {
      return strtoupper($value['type']) . '(' . implode(' ', $value['coordinates']) . ')';
    }
  }

}
