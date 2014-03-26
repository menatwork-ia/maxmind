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
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['maxmind'] = array
(
    // Export for members
    'maxmind_import' => array
    (
        'email_subject' => array
        (
            'new_rows',
            'domain',
            'websiteTitle',
            'error',
            'state',
            'work'
        ),
        'email_text' => array
        (
            'new_rows',
            'domain',
            'websiteTitle',
            'error',
            'state',
            'work'
        ),
        'email_html' => array
        (
            'new_rows',
            'domain',
            'websiteTitle',
            'error',
            'state',
            'work'
        )
    )
);