<?php

declare(strict_types = 1);

namespace Drupal\localgov_outpost_connector\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "outpost_json",
 *   title = @Translation("Outpost JSON")
 * )
 */
class OutpostJson extends Json {

  /**
   * Retrieves the JSON data and returns it as an array.
   *
   * @param string $url
   *   URL of a JSON feed.
   *
   * @throws \GuzzleHttp\Exception\RequestException
   */
  protected function getSourceData(string $url): array {
    $response = $this->getDataFetcherPlugin()->getResponseContent($url);

    // Convert objects to associative arrays.
    $source_data = json_decode($response, TRUE, 512, JSON_THROW_ON_ERROR);

    // If json_decode() has returned NULL, it might be that the data isn't
    // valid utf8 - see http://php.net/manual/en/function.json-decode.php#86997.
    if (is_null($source_data)) {
      $utf8response = utf8_encode($response);
      $source_data = json_decode($utf8response, TRUE, 512, JSON_THROW_ON_ERROR);
    }

    if (!is_array($source_data['content'])) {
      return [];
    }
    if ($this->itemSelector == 'content') {
      return $source_data['content'];
    }

    if ($this->itemSelector == 'meta') {
      // Do we need to implement this.
      return [];
    }

    // Entities embedded in the Service output that don't have endpoints - yet.
    $embed = [];
    foreach ($source_data['content'] as $row) {
      if (is_array($row[$this->itemSelector])) {
        foreach ($row[$this->itemSelector] as $item) {
          $embed[$item['id']] = $item;
        }
      }
    }
    return $embed;
  }

}
