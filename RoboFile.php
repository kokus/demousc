<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */

use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

/**
 * Robo Tasks.
 */
class RoboFile extends Tasks {

  /**
   * The path to custom modules.
   *
   * @var string
   */
  const CUSTOM_MODULES = __DIR__ . '/web/modules/custom';

  /**
   * The path to custom themes.
   *
   * @var string
   */
  const CUSTOM_THEMES = __DIR__ . '/web/themes/custom';

  /**
   * The theme machine name.
   *
   * @var string
   */
  const THEME_NAME = 'uscgov';

  /**
   * Local Project init.
   */
  public function localInit() {
    $this->say("Initializing new project...");
    $collection = $this->collectionBuilder();
    $collection
      ->taskComposerInstall()
      ->ignorePlatformRequirements()
      ->noInteraction()
      // ->addTaskList($this->prepareLocalFiles($site_name))
      ->addTask($this->localInstallDrupal());

    return $collection;
  }

  /**
   * Install local Drupal.
   *
   * @return \Robo\Task\Base\Exec
   *   A task to install Drupal.
   */
  protected function localInstallDrupal() {
    $task = $this->drush()
      ->args([
        'site-install',
        '--existing-config',
      ])
      ->option('verbose')
      ->option('yes');
    return $task;
  }

  /**
   * Local Site update.
   */
  public function localUpdate(ConsoleIO $io) {
    $io->title("Local site update starting...");
    $collection = $this->collectionBuilder($io);
    $collection->taskComposerInstall()
      ->addTask($this->runDeploy());
    return $collection;
  }

  /**
   * Site update.
   */
  public function appUpdate(ConsoleIO $io) {
    $io->title("Site update starting...");
    $collection = $this->collectionBuilder($io);
    $collection->taskComposerInstall()
      ->addTask($this->runDeploy())
      ->addTask($this->themeInit())
      ->addTask($this->themeBuild())
      ->addTaskList($this->fixContainerPerms());
    ;
    return $collection;
  }

  /**
   * Init theme.
   *
   * @param string $dir
   *   The directory to run the commands.
   *
   * @return \Robo\Collection\CollectionBuilder
   */
  public function themeInit($dir = '') {
    if (empty($dir)) {
      $dir = self::CUSTOM_THEMES . '/' . self::THEME_NAME;
    }
    $collection = $this->collectionBuilder();
    $collection->progressMessage('Init the theme...')
      ->taskNpmInstall()
      ->arg('--force')
      ->dir($dir);

    return $collection;
  }

  /**
   * Build theme.
   *
   * @param string $dir
   *   The directory to run the commands.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function themeBuild($dir = '') {
    if (empty($dir)) {
      $dir = self::CUSTOM_THEMES . '/' . self::THEME_NAME;
    }
    $collection = $this->collectionBuilder();
    $collection->progressMessage('Building the theme...')
      ->taskExec('npm run build')->dir($dir);

    return $collection;
  }


  /**
   * Fix Codesniffer errors.
   */
  public function phpcbf() {
    return $this->taskExec('vendor/bin/phpcbf')
      ->arg('-ns')
      ->printOutput(TRUE)
      ->run();
  }

  /**
   * Runs Codesniffer.
   */
  public function phpcs() {
    return $this->taskExec('vendor/bin/phpcs')
      ->arg('-ns')
      ->printOutput(TRUE)
      ->run();
  }

  /**
   * Runs phpstan.
   */
  public function analyse() {
    return $this->taskExec('vendor/bin/phpstan')
      ->arg('analyse')
      ->option('memory-limit', '500M')
      ->printOutput(TRUE)
      ->run();
  }

  /**
   * CI JOBS.
   */

  /**
   * Command to build the environment.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function ciBuild() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->copyConfigurationFiles());
    $collection->addTaskList($this->runComposer());
    return $collection->run();
  }

  /**
   * Command to run unit tests.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function ciUnitTests() {
    $collection = $this->collectionBuilder();
    $collection->addTask($this->runInstallDrupal());
    $collection->addTaskList($this->runUnitTests());
    return $collection->run();
  }

  /**
   * Serve Drupal.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function ciServeDrupal() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runServeDrupal());
    return $collection->run();
  }

  /**
   * Import database backup.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function ciImportDatabase() {
    return $this->taskExec('vendor/bin/drush sqlc < behat-db.sql');
  }

  /**
   * Install Drupal.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function ciInstallDrupal() {
    $collection = $this->collectionBuilder();
    $collection->addTask($this->runInstallDrupal());
    return $collection->run();
  }

  /**
   * Command to run Chrome headless.
   *
   * @return \Robo\Result
   *   The result tof the task
   */
  public function ciChromeHeadless() {
    return $this->taskExec('google-chrome-unstable --no-sandbox --disable-gpu --headless --window-size=1200,900 --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222 --disable-extensions --disable-dev-shm-usage')->run();
  }

  /**
   * Prepares code sniffer.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  public function ciPrepareCodeSniffer() {
    $collection = $this->collectionBuilder();
    $collection->taskExec('vendor/bin/phpcs --config-set installed_paths vendor/drupal/coder/coder_sniffer');
    $collection->taskExec('vendor/bin/phpcs -i');
    return $collection->run();
  }

  /**
   * Runs Behat tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  public function ciPrepareBehat() {
    $force = TRUE;
    $collection = $this->collectionBuilder();
    $collection->taskFilesystemStack()
      ->mkdir('screenshots');
    $collection->taskFilesystemStack()
      ->copy('.bitbucket/config/behat.yml', 'tests/behat.yml', $force);
    return $collection->run();
  }

  /**
   * Command to run behat tests.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function ciBehatTests() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runBehatTests());
    return $collection->run();
  }

  /**
   * Runs Behat tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runBehatTests() {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->mkdir('screenshots');
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.bitbucket/config/behat.yml', 'tests/behat.yml', $force);
    $tasks[] = $this->taskExec('sleep 30s');

    $base_url = 'http://localhost';
    $features_path = 'tests/features';

    $params = [
      'extensions' => [
        'Behat\MinkExtension' => [
          'base_url' => $base_url
        ],
      ],
      'suites' => [
        'default' => [
          'paths' => [$features_path]
        ]
      ]
    ];
    $params = json_encode($params);
    // Export json params and run behat test.
    $export_params = "export BEHAT_PARAMS='" . $params . "'";
    // Build command.
    $command = [
      $export_params,
      '&& vendor/bin/behat --verbose --colors -c tests/behat.yml'
    ];

    $tasks[] = $this->taskExec(implode(' ', $command));
    return $tasks;
  }

  /**
   * Run unit tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runUnitTests() {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.bitbucket/config/phpunit.xml', 'web/core/phpunit.xml', $force);
    $tasks[] = $this->taskExecStack()
      ->dir('web')
      ->exec('XDEBUG_MODE=coverage ../vendor/bin/phpunit -c core --debug --coverage-clover ../build/logs/clover.xml --verbose modules/custom');
    return $tasks;
  }

  /**
   * Install Drupal.
   *
   * @return \Robo\Task\Base\Exec
   *   A task to install Drupal.
   */
  protected function runInstallDrupal() {
    $task = $this->drush()
      ->args('site-install')
      ->option('verbose')
      ->option('yes');
    return $task;
  }

  /**
   * Serves Drupal.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runServeDrupal() {
    $tasks = [];
    $tasks[] = $this->taskExec('chown -R www-data:www-data ' . getenv('BITBUCKET_CLONE_DIR'));
    $tasks[] = $this->taskExec('ln -sf ' . getenv('BITBUCKET_CLONE_DIR') . '/web /var/www/html');
    $tasks[] = $this->taskExec('service apache2 start');
    return $tasks;
  }

  /**
   * Deploy site.
   *
   * @return \Robo\Task\Base\Exec
   *   An array of tasks.
   */
  protected function runDeploy() {
    $task = $this->drush()
      ->args('deploy');
    return $task;
  }

  /**
   * Runs composer commands.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runComposer() {
    $tasks = [];
    $tasks[] = $this->taskComposerValidate()->noCheckPublish();
    $tasks[] = $this->taskComposerInstall()
      ->noInteraction()
      ->envVars([
        'COMPOSER_ALLOW_SUPERUSER' => 1,
        'COMPOSER_DISCARD_CHANGES' => 1,
      ] + getenv())
      ->optimizeAutoloader();
    return $tasks;
  }

  /**
   * Copies configuration files.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function copyConfigurationFiles() {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->remove('web/sites/default/settings.db.php')
      ->remove('web/sites/default/files')
      ->copy('.bitbucket/config/settings.local.php', 'web/sites/default/settings.db.php');
    return $tasks;
  }

  /**
   * Prepares local files and folders.
   *
   * @param string $site_name
   *   Site name.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function prepareLocalFiles($site_name) {
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->mkdir('config');
    return $tasks;
  }

  /**
   * Return drush with default arguments.
   *
   * @return \Robo\Task\Base\Exec
   *   A drush exec command.
   */
  protected function drush() {
    return $this->taskExec('vendor/bin/drush');
  }

  /**
   * Fixes the container permissions.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function fixContainerPerms() {
    $tasks = [];
    $tasks[] = $this->taskExec("chown -R www-data web/sites/default");
    $tasks[] = $this->taskExec("chmod -R 775 web/sites/default");
    return $tasks;
  }

}
