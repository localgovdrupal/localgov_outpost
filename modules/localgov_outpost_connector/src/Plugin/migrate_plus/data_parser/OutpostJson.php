<?php

declare(strict_types = 1);

namespace Drupal\localgov_outpost_connector\Plugin\migrate_plus\data_parser;

use Drupal\migrate\MigrateException;
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
    $page = '';
    $pagination = [
      'number' => 0,
      'last' => null,
    ];
    $items = [];

    do {
      if ($pagination['number']) {
        $page = '?page=' . $pagination['number'] + 1;
      }
      $response = $this->getResponseContent($url . $page);

      // Convert objects to associative arrays.
      $source_data = json_decode($response, TRUE, 512, JSON_THROW_ON_ERROR);

      // If json_decode() has returned NULL, it might be that the data isn't
      // valid utf8 - see http://php.net/manual/en/function.json-decode.php#86997.
      if (is_null($source_data)) {
        $utf8response = utf8_encode($response);
        $source_data = json_decode($utf8response, TRUE, 512, JSON_THROW_ON_ERROR);
      }

      if (
        !is_array($source_data['content']) ||
        !isset($source_data['number']) ||
        !isset($source_data['last'])
      ) {
        throw MigrateException('Source data missing from ' . $url . $page);
      }
      $pagination = [
        'number' => $source_data['number'],
        'last' => $source_data['last'],
      ];
      if ($this->itemSelector == 'content') {
        $items = array_merge($items, $source_data['content']);
      }
      elseif ($this->itemSelector == 'meta') {
        // Do we need to implement this.
        // At present meta items are also added as 'fields' to the rows.
        // No use case yet to turn them into other entities.
        return [];
      }
      else {
        // Entities embedded in the Service output that don't have endpoints - yet.
        foreach ($source_data['content'] as $row) {
          if (is_array($row[$this->itemSelector])) {
            foreach ($row[$this->itemSelector] as $item) {
              $items[$item['id']] = $item;
            }
          }
        }
      }
    } while ($pagination['last'] === false);

    return $items;
  }

  /**
   * Local static cached response request.
   *
   * The data fetech plugin uses Drupal's Guzzle that can have caching
   * middleware. Good. But in this case we know we are likely to hit the
   * same url several times for the same data in the same session (when
   * parsing the same resuts but for different objects).
   */
  private function getResponseContent($url) {
    static $response_store = [];
    if (!isset($response_store[$url])) {
      $result = $this->getDataFetcherPlugin()->getResponseContent($url);
      $response_store[$url] = $result;
    }
    return $response_store[$url];
  }
}
