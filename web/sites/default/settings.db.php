<?php

// phpcs:ignoreFile
$databases['default']['default'] = [
  'database' => getenv('DRUPAL_DB_NAME'),
  'username' => getenv('DRUPAL_DB_USER'),
  'password' => getenv('DRUPAL_DB_PASS'),
  'prefix' => '',
  'host' => getenv('DRUPAL_DB_HOST'),
  'port' => getenv('DRUPAL_DB_PORT'),
  'isolation_level' => 'READ COMMITTED',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
];

$databases['migration']['default'] = [
  'database' => getenv('MIGRATION_DB_NAME'),
  'username' => getenv('MIGRATION_DB_USER'),
  'password' => getenv('MIGRATION_DB_PASS'),
  'prefix' => '',
  'host' => getenv('MIGRATION_DB_HOST'),
  'port' => getenv('MIGRATION_DB_PORT'),
  'isolation_level' => 'READ COMMITTED',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
];

$databases['migrate']['default'] = $databases['migration']['default'];