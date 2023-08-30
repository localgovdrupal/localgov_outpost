<?php

namespace Drupal\localgov_outpost_connector\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event fired when an derivative migrations are created for an endpoint.
 */
final class MigrationDeriverEvent extends Event {

  /**
   * MigrationDeriverEvent constructor.
   *
   * @param array $migration
   *   The migration being created.
   * @param string $originalId
   *   The original ID of the base migration.
   * @param int|string $directoryId
   *   The node ID of the directory.
   */
  public function __construct(protected array $migration, protected string $originalId, protected int|string $directoryId) {
  }

  /**
   * Gets the migration.
   *
   * @return array $migration
   *   The migration.
   */
  public function getMigration(): array {
    return $this->migration;
  }

  /**
   * Set the migration.
   *
   * @param array $migration
   *   The (altered) migration.
   */
  public function setMigration(array $migration): void {
    $this->migration = $migration;
  }

  /**
   * Get migration original ID.
   *
   * @return string
   *   The ID of the base migration.
   */
  public function getMigrationId(): string {
    return $this->originalId;
  }

  /**
   * Get directory node ID.
   *
   * @return int|string
   *   The directory ID for which this derivative is being generated.
   */
  public function getDirectoryId(): int|string {
    return $this->directoryId;
  }

}
