<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Maxmind
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'MaxMind',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MaxMind\MaxMindTable' => 'system/modules/maxmind/MaxMindTable.php',
	'MaxMind\MaxMind'      => 'system/modules/maxmind/MaxMind.php',
));
