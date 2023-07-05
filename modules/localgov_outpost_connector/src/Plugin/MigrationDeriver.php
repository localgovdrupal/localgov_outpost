<?php

declare(strict_types = 1);

namespace Drupal\localgov_outpost_connector\Plugin;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Create derivative migrations for Outpost URLs. 
 */
class MigrationDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Constructs new EntityViewDeriver.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Druapl\core\File\FileSystemInterface $fileSystem
   *   The file system service.
   */
  public function __construct(protected EntityTypeManagerInterface $entityTypeManager, protected FileSystemInterface $fileSystem, protected ExtensionPathResolver $extensionPathResolver) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_system'),
      $container->get('extension.path.resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    // Always rederive from scratch, because changes may have been made without
    // clearing our internal cache.
    $this->derivatives = [];

    // Find all directory channel nodes with outpost endpoints.
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->condition('type', 'localgov_directory');
    $query->condition('localgov_outpost_endpoint', NULL, 'IS NOT NULL');
    $query->condition('status', NodeInterface::PUBLISHED);
    $result = $query->execute();

    // Iterate over them.
    foreach (Node::loadMultiple($result) as $directory) {
      // Making all the required migrations.
      foreach ($this->getMigrations() as $migration) {
        // Creating a derivative ID.
        $id = $migration['id'] . '_' . $directory->id();
        // Source URI from the directory.
        $migration['source']['urls'] = [
          $directory->localgov_outpost_endpoint->uri,
        ];
        // Services node parent.
        if (isset($migration['process']['localgov_directory_channels'])) {
          $migration['process']['localgov_directory_channels']['default_value'] = $directory->id();
        }
        $this->derivatives[$id] = $migration;
      }
    }
    return $this->derivatives;
  }

  /**
   * Load array of migration configuration arrays.
   *
   * At the moment these are read from module yml files. Question if they should
   * be configurable, or if we want to add more configuration in the UI on the
   * node or elsewhere.
   *
   * @return array
   *   Array of migraiton configuration arrays.
   */
  private function getMigrations(): array {
    $dir = $this->extensionPathResolver->getPath('module', 'localgov_outpost_connector') . '/config/migration_base';
    $migrations= [];
    foreach ($this->fileSystem->scanDirectory($dir, '/\.yml/') as $path => $file) {
      $migrations[] = Yaml::decode(file_get_contents($path));

    }
    return $migrations;
  }

}
