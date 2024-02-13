<?php

namespace Drupal\usc_liveness\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Controller for viewing Liveness page.
 */
class LivenessPageController extends ControllerBase {

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

}
