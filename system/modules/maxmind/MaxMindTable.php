<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    maxmind
 * @license    GNU/LGPL
 * @filesource
 */

namespace MaxMind;

class MaxMindTable
{
    /**
     * Check if we have a notification. If active show the notification center options.
     */
    public function checkNotificationCenter()
    {
        // If the notification center is not in the active list remove it from the palettes.
        if (!self::isNotificationCenterActive())
        {
            $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace(array(',maxmind_nc', ',maxmind_serverName'), '', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);
        }
    }

    /**
     * Check if the notification center is a active module.
     *
     * @return bool True it is.
     */
    public static  function isNotificationCenterActive()
    {
        $arrActiveModules = \Config::getInstance()->getActiveModules();

        // If the notification center is not in the active list remove it from the palettes.
        if (!in_array('notification_center', $arrActiveModules))
        {
            return false;
        }

        return true;
    }

    /**
     * Get all available mail templates from notification center.
     *
     * @return array A list with all found mail templates from nc.
     */
    public function getMailTemplates()
    {
        // Check if we have this db.
        if (!\Database::getInstance()->tableExists('tl_nc_notification'))
        {
            return array();
        }

        // Get all values...
        $arrReturn    = array();
        $objTemplates = \Database::getInstance()
            ->prepare('SELECT * FROM tl_nc_notification WHERE type = "maxmind_import" ORDER BY title ASC')
            ->execute();

        while ($objTemplates->next())
        {
            $arrReturn[$objTemplates->id] = $objTemplates->title;
        }

        // ... and return them.
        return $arrReturn;
    }
} 