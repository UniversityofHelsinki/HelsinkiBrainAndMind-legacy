<?php

// Use druidfi/omen to auto-configure Drupal
//
// You can setup project specific configuration in this directory:
//
// ENV.settings.php and ENV.services.yml
// and
// local.settings.php and local.service.yml
//
// These files are loaded automatically if found.
//
extract((new Druidfi\Omen\DrupalEnvDetector(__DIR__))->getConfiguration());

$settings['config_sync_directory'] = '../conf/cmi';

if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
