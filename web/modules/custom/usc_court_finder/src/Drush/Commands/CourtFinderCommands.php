<?php

namespace Drupal\usc_court_finder\Drush\Commands;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\usc_court_finder\CourtFinderImportBatchService;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Court Finder commands for Drush.
 */
class CourtFinderCommands extends DrushCommands {

  use StringTranslationTrait;

  /**
   * Imports Autocompletes from a CSV file.
   *
   * @throws \Exception
   */
  #[CLI\Command(name: 'usc:import:autocompletes', aliases: ['uiau'])]
  #[CLI\Argument(name: 'importUri', description: 'The path or URI for the CSV file to be imported.')]
  #[CLI\Usage(name: 'usc:import:autocompletes "path_to_file/file.csv"', description: 'Usage example with import_uri.')]
  public function importAutocompletes(string $importUri): void {
    $this->basicImport($importUri, 'usc_autocomplete');
  }

  /**
 * Imports Locations from a CSV file.
 *
 * @throws \Exception
 */
  #[CLI\Command(name: 'usc:import:locations', aliases: ['uilo'])]
  #[CLI\Argument(name: 'importUri', description: 'The path or URI for the CSV file to be imported.')]
  #[CLI\Usage(name: 'usc:import:locations "path_to_file/file.csv"', description: 'Usage example with import_uri.')]
  public function importLocations(string $importUri): void {
    $this->basicImport($importUri, 'usc_location');
  }

  /**
   * Imports Circuits from a CSV file.
   *
   * @throws \Exception
   */
  #[CLI\Command(name: 'usc:import:circuits', aliases: ['uici'])]
  #[CLI\Argument(name: 'importUri', description: 'The path or URI for the CSV file to be imported.')]
  #[CLI\Usage(name: 'usc:import:circuits "path_to_file/file.csv"', description: 'Usage example with import_uri.')]
  public function importCircuits(string $importUri): void {
    $this->basicImport($importUri, 'usc_circuit');
  }

  /**
   * Imports Counties from a CSV file.
   *
   * @throws \Exception
   */
  #[CLI\Command(name: 'usc:import:counties', aliases: ['uico'])]
  #[CLI\Argument(name: 'importUri', description: 'The path or URI for the CSV file to be imported.')]
  #[CLI\Usage(name: 'usc:import:counties "path_to_file/file.csv"', description: 'Usage example with import_uri.')]
  public function importCounties(string $importUri): void {
    $this->basicImport($importUri, 'usc_county');
  }

  /**
   * Imports Districts from a CSV file.
   *
   * @throws \Exception
   */
  #[CLI\Command(name: 'usc:import:districts', aliases: ['uidi'])]
  #[CLI\Argument(name: 'importUri', description: 'The path or URI for the CSV file to be imported.')]
  #[CLI\Usage(name: 'usc:import:districts "path_to_file/file.csv"', description: 'Usage example with import_uri.')]
  public function importDistricts(string $importUri): void {
    $this->basicImport($importUri, 'usc_district');
  }

  /**
   * Imports States from a CSV file.
   *
   * @throws \Exception
   */
  #[CLI\Command(name: 'usc:import:states', aliases: ['uist'])]
  #[CLI\Argument(name: 'importUri', description: 'The path or URI for the CSV file to be imported.')]
  #[CLI\Usage(name: 'usc:import:states "path_to_file/file.csv"', description: 'Usage example with import_uri.')]
  public function importStates(string $importUri): void {
    $this->basicImport($importUri, 'usc_state');
  }

  /**
   * A helper function to import entities from a CSV file.
   *
   * @throws \Exception
   */
  private function basicImport(string $importUri, string $entityType): void {
    // Parse the file to get data.
    $file = fopen($importUri, 'r');
    if ($file) {
      $parsed = [];
      while (($row = fgetcsv($file)) !== FALSE) {
        $parsed[] = $row;
      }
      if (count($parsed) > 0) {
        $headers = array_shift($parsed);
        if (!$this->validateHeaders($headers, $entityType)) {
          $this->logger()->error('Some primary keys are missing in the file headers.');
        }
        else {
          $batchBuilder = new BatchBuilder();
          $this->logger()->notice("Batch operations start.");
          foreach ($parsed as $row) {
            $idKeys = $this->getPrimaryKeys($entityType);
            $headersMapping = $this->getHeadersMapping($entityType);
            $fields = array_combine($headers, $row);
            $options = [
              $entityType,
              $idKeys,
              $headersMapping,
              $fields,
            ];
            $batchBuilder->addOperation(
              [
                CourtFinderImportBatchService::class,
                'processImportItem'
              ], $options);
          }

          $batchBuilder
            ->setTitle($this->t('Importing entity @type', ['@type' => $entityType]))
            ->setFinishCallback(
              [
                CourtFinderImportBatchService::class,
                'processFinished'
              ]
            )
            ->setErrorMessage($this->t('Batch has encountered an error'));
          batch_set($batchBuilder->toArray());
          drush_backend_batch_process();
          $this->logger()->notice("Batch operations end.");
        }
      }
      else {
        $this->logger()->warning('The import file is empty. Nothing to import.');
      }
    }
    else {
      $this->logger()->error('Can not open the file from the provided url.');
    }
  }

  /**
   * Checks if headers are fully present in Primary Keys.
   *
   * @throws \Exception
   */
  private function validateHeaders(array $headers, string $entityType): bool {
    return !empty($headers) && (count($this->getPrimaryKeys($entityType)) === count(array_intersect($headers, $this->getPrimaryKeys($entityType))));
  }

  /**
   * A helper function to get primary keys for an entity type.
   *
   * @throws \Exception
   */
  private function getPrimaryKeys(string $entityType): array {
    switch ($entityType) {
      case 'usc_location':
        return ['LocationId'];

      case 'usc_autocomplete':
        return ['term', 'type'];

      case 'usc_circuit':
        return ['circuit_code'];

      case 'usc_county':
        return ['county_name', 'state_code'];

      case 'usc_district':
        return ['district_code'];

      case 'usc_state':
        return ['state_code'];

      default:
        throw new \Exception('Unknown entity type');
    }
  }

  /**
   * A helper function to get mapping between D7 column names and D10 column names.
   *
   * @throws \Exception
   */
  private function getHeadersMapping(string $entityType): array {
    switch ($entityType) {
      case 'usc_location':
        return [
          'OrgCode' => 'org_code',
          'Unit' => 'unit',
          'CourtType' => 'court_type',
          'LocationId' => 'location_id',
          'Circuit' => 'circuit',
          'District' => 'district',
          'MainOffice' => 'main_office',
          'OfficeName' => 'office_name',
          'Url' => 'url',
          'Phone' => 'phone',
          'Fax' => 'fax',
          'Opens' => 'opens',
          'Closes' => 'closes',
          'LunchStart' => 'lunch_start',
          'LunchEnd' => 'lunch_end',
          'HasPublicLibrary' => 'has_public_library',
          'Longitude' => 'longitude',
          'Latitude' => 'latitude',
          'Room' => 'room',
          'BuildingName' => 'building_name',
          'BuildingAddress' => 'building_address',
          'BuildingCity' => 'building_city',
          'BuildingState' => 'building_state',
          'BuildingZip' => 'building_zip',
          'LocationGroup' => 'location_group',
          'Address' => 'location_group',
          'EJurorUrl' => 'ejuror_url',
          'JuryServiceUrl' => 'jury_service_url',
          'ECFurl' => 'ecf_url',
          'ECFUrl2' => 'ecf_url2',
          'MailBox' => 'mail_box',
          'MailZip' => 'mail_zip',
        ];

      case 'usc_autocomplete':
        return [
          'term' => 'term',
          'type' => 'type',
          'weight' => 'weight',
        ];

      case 'usc_circuit':
        return [
          'circuit_code' => 'code',
          'circuit_name' => 'name',
        ];

      case 'usc_county':
        return [
          'county_name' => 'name',
          'state_code' => 'state_code',
          'district_code' => 'district_code',
        ];

      case 'usc_district':
        return [
          'district_code' => 'code',
          'district_name' => 'name',
          'state_code' => 'state_code',
          'latitude' => 'latitude',
          'longitude' => 'longitude',
        ];

      case 'usc_state':
        return [
          'state_code' => 'code',
          'state_name' => 'name',
          'circuit_code' => 'circuit_code',
        ];

      default:
        throw new \Exception('Unknown entity type');
    }
  }

}
