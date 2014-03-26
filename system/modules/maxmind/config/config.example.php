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
 * Cron jobs
 */
$GLOBALS['TL_CRON']['monthly'][]    = array('MaxMind\MaxMind', 'run');
$GLOBALS['TL_CRON']['weekly'][]     = array('MaxMind\MaxMind', 'run');
$GLOBALS['TL_CRON']['daily'][]      = array('MaxMind\MaxMind', 'run');

// Contao 3 only
$GLOBALS['TL_CRON']['hourly'][]     = array('MaxMind\MaxMind', 'run');
$GLOBALS['TL_CRON']['minutely'][]   = array('MaxMind\MaxMind', 'run');