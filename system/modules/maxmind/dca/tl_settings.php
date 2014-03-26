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
 * onload_callback for removing the notification center if not active.
 */
$GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'][] = array('\MaxMind\MaxMindTable', 'checkNotificationCenter');

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{maxmind_legend},maxmind_url,maxmind_has_license,,maxmind_lastUpdate,maxmind_nc';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][]         = 'maxmind_has_license';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['maxmind_has_license'] = 'maxmind_license';

/**
 * Add field
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['maxmind_url'] = array
(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['maxmind_url'],
    'explanation'       => 'maxmind_url',
    'exclude'           => true,
    'inputType'         => 'text',
    'eval'              => array
    (
        'tl_class'           => 'long',
        'mandatory'          => true,
        'helpwizard'         => true,
        'preserveTags'       => true,
        'decodeEntities'     => true
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['maxmind_has_license'] = array
(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['maxmind_has_license'],
    'exclude'           => true,
    'inputType'         => 'checkbox',
    'eval'              => array
    (
        'tl_class'           => 'm12 w50',
        'submitOnChange'     => true
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['maxmind_license'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['maxmind_license'],
    'exclude'           => true,
    'inputType'         => 'text',
    'eval'              => array
    (
        'tl_class'           => 'w50',
        'mandatory'          => true
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['maxmind_nc'] = array
(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['maxmind_nc'],
    'exclude'           => true,
    'inputType'         => 'select',
    'options_callback'  => array('\MaxMind\MaxMindTable', 'getMailTemplates'),
    'eval'              => array
    (
        'tl_class'           => 'w50',
        'includeBlankOption' => true
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['maxmind_lastUpdate'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['maxmind_lastUpdate'],
    'exclude'           => true,
    'inputType'         => 'text',
    'eval'              => array
    (
        'tl_class'           => 'w50',
        'readonly'           => true,
        'disabled'           => true
    )
);
