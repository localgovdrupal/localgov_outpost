<?php

namespace Drupal\localgov_outpost_connector\Event;

/**
 * Defines events for the LocalGov Outpost Connector.
 */
final class OutpostConnectorEvents {

  /**
   * Event fired when derivative connectors are created for each URL.
   *
   * @see \Drupal\localgov_outpost_connector\Event\OutpostConnectorEvents
   * @see \Drupal\localgov_outpost_connector\Plugin\MigrationDeriver::getDerivativeDefinitions()
   */
  const MIGRATION_DERIVER = 'migration_deriver';
}
