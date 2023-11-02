<?php

namespace Drupal\usc_court_finder;

/**
 * Class CourtFinderImportBatchService.
 */
class CourtFinderImportBatchService {

 /**
   * Batch process callback.
   *
   * @param string $entityType
   *  Import entity type.
   * @param array $idKeys
   *  Import entity primary keys.
   * @param array $headersMapping
   *  A mapping array between D7 and D10 column names.
   * @param array $fields
   *  An array with entity values.
   * @param object $context
   *   Context for operations.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \RuntimeException
   */
  public static function processImportItem(string $entityType, array $idKeys, array $headersMapping, array $fields, &$context) {

    $ids = [];
    $entityTypeManager = \Drupal::entityTypeManager();

    foreach ($idKeys as $key) {
      $ids[$headersMapping[$key]] = $fields[$key];
    }

    $storage = $entityTypeManager->getStorage($entityType);
    $entity = $storage->loadByProperties($ids);
    if (!$entity) {
      $entity = $storage->create($ids);
    }
    elseif (count($entity) > 1) {
        // Throw an exception if we have a collision but it should not happen.
        throw new \RuntimeException('There is more than one entity with primary keys.');
    }
    else {
        $entity = reset($entity);
    }
    foreach ($fields as $fieldName => $value) {
      $entity->{$headersMapping[$fieldName]} = $value;
    }
    $entity->save();
    $message = t('Imported @entity_type: @id', ['@entity_type' => $entityType, '@id' => $entity->id()]);
    $context['results'][] = $entity->id();
    $context['message'] = $message;
  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public static function processFinished($success, $results, $operations) {
    $message = NULL;
    $messenger = \Drupal::messenger();
    if ($success) {
      $message = t('@count results processed.', ['@count' => count($results)]);
    } else {
      $message = t('Finished with an error.');
    }
    $messenger->addMessage($message);
  }

}
