<?php

namespace Drupal\usc_extra\Controller;

use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\geocoder\Entity\GeocoderProvider;
use Drupal\usc_court_finder\CourtFinderImportBatchService;
use Drupal\usc_court_finder\Drush\Commands\CourtFinderCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Controller for viewing extra pages.
 */
class ExtraPagesController extends ControllerBase {

  /**
   * Liveness page.
   *
   * @throws \InvalidArgumentException
   * @throws \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
   */
  public function liveness() {

    // Clear PHP's file status cache (see
    // https://www.php.net/manual/en/function.clearstatcache.php.
    clearstatcache(TRUE, 'liveness.gif');

    if (file_exists(DRUPAL_ROOT . '/liveness.gif')) {

      $data = [
        'date' => date(DATE_COOKIE),
      ];

      $headers = [
        'X-Robots-Tag' => 'noindex, nofollow',
      ];

      return new JsonResponse(data: $data, headers: $headers);
    }
    else {
      // liveness.gif has moved; return a 503 Service Unavailable.
      throw new ServiceUnavailableHttpException();
    }
  }

  /**
   * Failtest page.
   */
  public function failtest() {

    $config = $this->config('usc_extra.settings');

    // Get the failtest message (or a default if it is empty).
    $message = $config->get('failtest_message');
    $server = $config->get('failtest_server');

    // Return the message as part of a renderable array.
    return [
      '#markup' => $message . '<br />' . $server,
    ];
  }

}
