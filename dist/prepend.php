<?php

require_once dirname(__FILE__) . '/config.php';

$site = Zend_Registry::get('site');

$application = $site->getInstance( dirname(__FILE__) . '/..' );
Zend_Registry::set('application', $application);

$configuration = $application->getConfiguration();

if (empty($configuration['bmUrl']) && !empty($configuration['bmUsername'])) {
    $configuration['bmUrl'] = 'http://blogmarks.net/user/' . $configuration['bmUsername'];
}

if (!Zend_Registry::isRegistered('cache')) {
    $cacheDirectory = LD_TMP_DIR . '/cache/';
    Ld_Files::createDirIfNotExists($cacheDirectory);
    $frontendOptions = array('lifetime' => 300, 'automatic_serialization' => true);
    $backendOptions = array('cache_dir' => $cacheDirectory);
    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    Zend_Registry::set('cache', $cache);
}
