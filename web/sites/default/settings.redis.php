<?php

// phpcs:ignoreFile

// Set Redis as the default backend for any cache bin not otherwise specified.
$settings['cache']['default'] = 'cache.backend.redis';
$settings['redis.connection']['host'] = getenv('DRUPAL_REDIS_HOST');
$settings['redis.connection']['port'] = getenv('DRUPAL_REDIS_PORT');

// You can leverage Redis by using it for the lock and flood control systems
// and the cache tag checksum.
// To do so, apply the following changes to the container configuration.
// Alternatively, copy the contents of the modules/contrib/redis/example.services.yml file
// to your project-specific services.yml file.
// Modify the contents to fit your needs and remove the following line.
$settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';
// Allow the services to work before the Redis module itself is enabled.
$settings['container_yamls'][] = 'modules/contrib/redis/redis.services.yml';

// To enable, set the minimal length after which the cached data should be
// compressed:
$settings['redis_compress_length'] = 100;

// By default, compression level 1 is used, which provides considerable storage
// optimization with minimal CPU overhead, to change:
$settings['redis_compress_level'] = 6;

// To use Redis for container cache, add the classloader path manually.
$class_loader->addPsr4('Drupal\\redis\\', 'modules/contrib/redis/src');
// Use Redis for container cache.
// The container cache is used to load the container definition itself.
// This means that any configuration stored in the container isn't available
// until the container definition is fully loaded.
// To ensure that the container cache uses Redis rather than the
// default SQL cache, add the following lines.
$settings['bootstrap_container_definition'] = [
  'parameters' => [],
  'services' => [
    'redis.factory' => [
      'class' => 'Drupal\redis\ClientFactory',
    ],
    'cache.backend.redis' => [
      'class' => 'Drupal\redis\Cache\CacheBackendFactory',
      'arguments' => ['@redis.factory', '@cache_tags_provider.container', '@serialization.phpserialize'],
    ],
    'cache.container' => [
      'class' => '\Drupal\redis\Cache\PhpRedis',
      'factory' => ['@cache.backend.redis', 'get'],
      'arguments' => ['container'],
    ],
    'cache_tags_provider.container' => [
      'class' => 'Drupal\redis\Cache\RedisCacheTagsChecksum',
      'arguments' => ['@redis.factory'],
    ],
    'serialization.phpserialize' => [
      'class' => 'Drupal\Component\Serialization\PhpSerialize',
    ],
  ],
];
