<?php

namespace Drupal\usc_court_finder\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the County entity.
 *
 * @ContentEntityType(
 *   id = "usc_county",
 *   label = @Translation("County"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\usc_court_finder\CountyListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\usc_court_finder\CourtFinderAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "usc_county",
 *   admin_permission = "administer usc_court_finder",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/usc_court_finder/county/{usc_county}",
 *     "edit-form" = "/usc_court_finder/county/{usc_county}/edit",
 *     "delete-form" = "/usc_court_finder/county/{usc_county}/delete",
 *     "add-form" = "/usc_court_finder/county/add",
 *     "collection" = "/admin/content/usc_court_finder/county",
 *   },
 *   field_ui_base_route = "usc_court_finder.county.settings"
 * )
 */
final class County extends ContentEntityBase implements CourtFinderEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('Name'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['state_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('State ID'))
      ->setDescription(t('The State ID, which the district belongs to.'))
      ->setSettings([
        'target_type' => 'usc_state',
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['district_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('District ID'))
      ->setDescription(t('The District ID, which the county belongs to.'))
      ->setSettings([
        'target_type' => 'usc_district',
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
