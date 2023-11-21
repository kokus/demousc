<?php

namespace Drupal\usc_migrate_gutenberg\EventSubscriber;

use Drupal\html_processors\Service\HtmlGutenbergParser;
use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class USCGutenbergMigrationSubscriber.
 */
class USCGutenbergMigrationSubscriber implements EventSubscriberInterface {

  /**
   * The plugin.manager.html_gutenberg_parser service.
   *
   * @var \Drupal\html_processors\Service\HtmlGutenbergParser
   */
  protected $htmlGutenbergParser;

  /**
   * Constructs a new USCGutenbergMigrationSubscriber object.
   */
  public function __construct(HtmlGutenbergParser $html_gutenberg_parser) {
    $this->htmlGutenbergParser = $html_gutenberg_parser;
  }

  /**
   * Field names to migrate.
   *
   * @var array
   */
  protected $fieldNames = [
    "body",
  ];

  /**
   * Gutenberg processors configuration.
   *
   * @var array
   */
  protected $processorConfiguration = [
    "map" => [
      "disabled" => 0,
      "id" => "map",
    ],
    "table_cell" => [
      "disabled" => FALSE,
      "id" => "table_cell",
      "allowed_node_names" => "a #text strong img br",
      "allowed_attributes" => "class",
    ],
    "image" => [
      "remote_url" => "https://uscourts-dev.agileana.com",
      "id" => "image",
      "disabled" => FALSE,
    ],
    "heading" => [
      "id" => "heading",
      "disabled" => FALSE,
    ],
    "ordered_list" => [
      "id" => "ordered_list",
      "disabled" => 0,
    ],
    "unordered_list" => [
      "id" => "unordered_list",
      "disabled" => 0,
    ],
    "list_item" => [
      "id" => "list_item",
      "disabled" => 0,
    ],
    "separator" => [
      "id" => "separator",
      "disabled" => 0,
    ],
    "table" => [
      "id" => "table",
      "disabled" => FALSE,
      "first_row_to_thead" => TRUE,
      "header_attribute" => "",
    ],
    "paragraph" => [
      "id" => "paragraph",
      "disabled" => 0,
    ],
    "container" => [
      "id" => "container",
      "disabled" => 0,
    ]
  ];

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::PREPARE_ROW] = "onPrepareRow";
    return $events;
  }

  /**
   * This method is called when the onPrepareRow is dispatched.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The dispatched event.
   */
  public function onPrepareRow(MigratePrepareRowEvent $event) {
    $this->migrateFormatterMigrations($event);
  }

  /**
   * Alter field instance migrations.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The migrate row event.
   *
   * @throws \Exception
   *   If the source cannot be changed.
   */
  private function migrateFormatterMigrations(MigratePrepareRowEvent $event) {
    $row = $event->getRow();
    foreach ($this->fieldNames as $key => $field) {
      $text = current($row->getSourceProperty($field));
      if (!empty($text["value"]) && !empty($text["format"])) {
        // Process html with Gutenberg processors.
        $parsedText = $this->htmlGutenbergParser->parse($text["value"], $this->processorConfiguration);
        $row->setSourceProperty($field, [
          "value" => $parsedText,
          "format" => "gutenberg",
          "summary" => $text["summary"],
        ]);
      }
    }
  }

}
