<?php

namespace Drupal\piksel\Plugin\media\PikselProvider;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Site\Settings;
use Drupal\piksel\Piksel;
use Drupal\piksel\Plugin\PikselProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a piksel media provider for Program.
 *
 * @PikselProvider(
 *   id = "program",
 *   label = "Program",
 * )
 */
class Program extends PluginBase implements PikselProviderInterface, ContainerFactoryPluginInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a new OEmbed instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\Client $http_client
   *   The HTTP client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * Make a request to the piksel API v5.
   *
   * Search for projects.
   *
   * @throws \Exception
   * @throws GuzzleException
   */
  public function searchProjects(): array {
    $response = $this->httpClient->get('https://player.piksel.tech/ws/ws_vod_project/api/bd1863e1-a59e-11e3-bfa8-005056865f49/mode/json/apiv/5');

    $data = Json::decode($response->getBody()->getContents());
    return $data['response']['WsVodProjectResponse']['projects'];
  }

  /**
   * Make a request to the piksel API v5.
   *
   * Search for programs.
   *
   * @throws \Exception
   * @throws GuzzleException
   */
  public function searchPrograms(String $projectId): array {
    $response = $this->httpClient->get('https://player.piksel.tech/ws/ws_program/api/bd1863e1-a59e-11e3-bfa8-005056865f49/mode/json/apiv/5?p=' . $projectId);

    $data = Json::decode($response->getBody()->getContents());
    return $data['response']['WsProgramResponse']['programs'];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   * @throws GuzzleException
   */
  public function load(string $id): Piksel {
    $response = $this->httpClient->get('https://player.piksel.tech/ws/ws_program/api/bd1863e1-a59e-11e3-bfa8-005056865f49/mode/json/apiv/5?v=' . $id);

    $data = Json::decode($response->getBody()->getContents());
    $program_object = $data['response']['WsProgramResponse']['program'];

    return new Piksel(
      $id,
      $program_object['thumbnailUrl'],
      $program_object['posterUrl'],
      $program_object['Description'],
      $program_object['Title'],
    );
  }

}
