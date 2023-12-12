<?php

namespace Drupal\usc_migrate_adjustments\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\migrate_plus\Event\MigrateEvents as MigratePlusEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adjust migrations event subscriber.
 */
class AdjustMigrationSubscriber implements EventSubscriberInterface {

  /**
   * The module config.
   *
   * @var array
   */
  protected $adjustMigrationSettings = [];

  /**
   * Constructs a new AdjustMigrationSubscriber instance.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $migration_plugin_manager
   *   The migration plugin manager.
   * @param \Drupal\migrate\MigrateLookupInterface $migrate_lookup
   *   The migrate lookup service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The entity type manager service.
   */
  public function __construct(
    private MigrationPluginManagerInterface $migration_plugin_manager,
    private MigrateLookupInterface $migrate_lookup,
    ConfigFactoryInterface $configFactory) {
    $this->adjustMigrationSettings = $configFactory->get('usc_migrate_adjustment.settings');
  }

  /**
   * Migrate prepare row event handler.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The prepare row event.
   *
   * @throws \Exception
   *   If the row is empty.
   */
  public function onPrepareRow(MigratePrepareRowEvent $event) {
    $this->adjustMigrationRows($event);
  }

  /**
   * Performs migration adjustments to fix the different issues.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The prepare row event.
   *
   * @throws \Exception
   *   If the row is empty.
   */
  private function adjustMigrationRows(MigratePrepareRowEvent $event) {
    $row = $event->getRow();
    $source = $event->getSource();

    $pluginId = $source->getPluginId();

    // Transform entity reference fields pointing to bean entities so
    // they point to block_content ones.
    if (($pluginId == 'd7_field') && ($row->getSourceProperty('type') == 'entityreference')) {
      $settings = $row->getSourceProperty('settings');
      if ($settings['target_type'] == 'bean') {
        $settings['target_type'] = 'block_content';
        $row->setSourceProperty('settings', $settings);
        $row->setDestinationProperty('settings', $settings);
      }
    }

    $plugins = ['d7_field', 'd7_field_instance'];

    // Instances which cause MigrateSkipRowException exection.
    // see \Drupal\text\Plugin\migrate\field\d7\TextField::getFieldType()
    // line 97 throws new MigrateSkipRowException.
    if ((in_array($pluginId, $plugins)) && ($row->getSourceProperty('field_name') == 'field_fc_be_body')) {
      $instances = $row->getSourceProperty('instances');
      $updated = FALSE;
      foreach ($instances as &$instance) {

        $data = unserialize($instance['data'], ['allowed_classes' => FALSE]);
        if ($data['settings']['text_processing'] == '0') {
          $data['settings']['text_processing'] = '1';
          $instance['data'] = serialize($data);
          $updated = TRUE;
        }
      }

      if ($updated) {
        $row->setSourceProperty('instances', $instances);
      }
    }

    // Fix a form widget for tags.
    if (($pluginId == 'd7_field_instance_widget_settings') && ($row->getSourceProperty('field_name') == 'field_tags')) {
      $row->setSourceProperty('type', 'entity_reference_autocomplete_tags');
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MigratePlusEvents::PREPARE_ROW => ['onPrepareRow'],
    ];
  }

}
