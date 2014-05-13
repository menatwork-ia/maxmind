<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    maxmind
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Notification center configuration
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['maxmind']['maxmind_import'] = array
(
    'email_subject' => array
    (
        'new_rows',
        'domain',
        'websiteTitle',
        'error',
        'state',
        'work',
        'servername'
    ),
    'email_text'    => array
    (
        'new_rows',
        'domain',
        'websiteTitle',
        'error',
        'state',
        'work',
        'servername'
    ),
    'email_html'    => array
    (
        'new_rows',
        'domain',
        'websiteTitle',
        'error',
        'state',
        'work',
        'servername'
    )
);

$GLOBALS['SYC_CONFIG']['folder_blacklist'] = array_merge( (array) $GLOBALS['SYC_CONFIG']['folder_blacklist'], array
(
    'system/modules/maxmind/assets/',
));

$GLOBALS['SYC_CONFIG']['local_blacklist'] = array_merge( (array) $GLOBALS['SYC_CONFIG']['local_blacklist'], array
(
    'maxmind_lastUpdate',
    'maxmind_serverName'
));
