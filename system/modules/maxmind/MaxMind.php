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

use NotificationCenter\Model\Notification;

/**
 * Class PurgeLog
 */
class MaxMind extends \System
{

    /**
     * Contains the url for the zip file for downloading.
     *
     * @var string
     */
    protected $strDownloadUrl;

    /**
     * Contains the license key for the zip file.
     *
     * @var string
     */
    protected $strLicenseKey;


    /**
     * Contains the realname of the CSV file.
     *
     * @var string
     */
    protected $strRealNameOfCsv = '';

    /**
     * Path to the download folder.
     *
     * @var string
     */
    protected $strDownloadFolder = 'system/modules/maxmind/assets';

    /**
     * Name of the zip file on this system.
     *
     * @var string
     */
    protected $strNameOfTempZipFile = 'tmp.zip';

    /**
     * Name of the csv file on this system.
     *
     * @var string
     */
    protected $strNameOfTempCsvFile = 'geoData.csv';

    /**
     * Counte the rows of import.
     *
     * @var int
     */
    protected $intTotalInsert = 0;

    /**
     * Flag if each log should dump.
     *
     * @var bool
     */
    protected $blnShowLogs = false;

    /**
     * If set the system downloads the file without the import in the database.
     *
     * @var bool
     */
    protected $blnDryRun = false;

    /**
     * If set the system won't send a mail.
     *
     * @var bool
     */
    protected $blnNoMail = false;

    /**
     * If set the system will always import the csv.
     *
     * @var bool
     */
    protected $blnForceUpdate = false;

    /**
     * If we have the free version of maxmind, don't update the entry for the last file.
     * This only works for the paid version.
     *
     * @var bool
     */
    protected $blnNoUpdateLastFile = false;

    /**
     * Initialize
     */
    public function __construct()
    {
        // Call parent.
        parent::__construct();

        // Load some language files.
        $this->loadLanguageFile('default');

        // Get the entries from the localconfig and check them.
        $strDownloadUrl = $GLOBALS['TL_CONFIG']['maxmind_url'];
        if (!empty($strDownloadUrl) && \Validator::isUrl($strDownloadUrl))
        {
            $this->strDownloadUrl = $strDownloadUrl;
        }

        // Check if we have a license key.
        if ($GLOBALS['TL_CONFIG']['maxmind_has_license'])
        {
            $strLicenseKey = $GLOBALS['TL_CONFIG']['maxmind_license'];
            if (!empty($strLicenseKey))
            {
                $this->strLicenseKey = $strLicenseKey;
            }
        }

        $this->intNotificationID = $GLOBALS['TL_CONFIG']['maxmind_nc'];
    }

    /**
     * @param boolean $blnShowLogs
     */
    public function setShowLogs($blnShowLogs)
    {
        $this->blnShowLogs = $blnShowLogs;
    }

    /**
     * @return boolean
     */
    public function getShowLogs()
    {
        return $this->blnShowLogs;
    }

    /**
     * @param boolean $blnDryRun
     */
    public function setDryRun($blnDryRun)
    {
        $this->blnDryRun = $blnDryRun;
    }

    /**
     * @return boolean
     */
    public function getDryRun()
    {
        return $this->blnDryRun;
    }

    /**
     * @param boolean $blnNoMail
     */
    public function setNoMail($blnNoMail)
    {
        $this->blnNoMail = $blnNoMail;
    }

    /**
     * @return boolean
     */
    public function getNoMail()
    {
        return $this->blnNoMail;
    }

    /**
     * @param boolean $blnForceUpdate
     */
    public function setForceUpdate($blnForceUpdate)
    {
        $this->blnForceUpdate = $blnForceUpdate;
    }

    /**
     * @return boolean
     */
    public function getForceUpdate()
    {
        return $this->blnForceUpdate;
    }

    /**
     * Add a log entry to the database
     *
     * @param string $strText     The log message
     *
     * @param string $strFunction The function name
     *
     * @param string $strCategory The category name
     */
    protected function addLog($strText, $strFunction, $strCategory)
    {
        $this->log($strText, $strFunction, $strCategory);

        // If flag is set display the log entry.
        if ($this->blnShowLogs)
        {
            echo sprintf('%s (%s): %s', $strCategory, $strFunction, $strText);
            echo PHP_EOL;
        }
    }

    /**
     * Check if we have this file already in DB
     *
     * @return boolean
     */
    protected function isDatabaseUpToDate()
    {
        // Check if we have some information in DB
        $objCount = \Database::getInstance()
            ->prepare("SELECT COUNT(*) as count FROM tl_geodata")
            ->execute();

        if ($objCount->count == 0)
        {
            return false;
        }

        // Check the name of the last file
        if ($GLOBALS['TL_CONFIG']['maxmind_lastUpdate'] == $this->strRealNameOfCsv)
        {
            return true;
        }

        return false;
    }

    /**
     * Check if all needed params are valid. If not throw a exception.
     *
     * @throws \RuntimeException If a param is not valid.
     */
    protected function checkParams()
    {
        if (empty($this->strDownloadUrl))
        {
            throw new \RuntimeException('Download url is empty. Maybe not valid.');
        }

        if (!\Validator::isUrl($this->strDownloadUrl))
        {
            throw new \RuntimeException('Download url is not valid: ' . $this->strDownloadUrl);
        }
    }

    /**
     * Run the import.
     */
    public function run()
    {
        try
        {
            // Check if we have all vars
            $this->checkParams();

            // Check folder.
            $this->checkDownloadFolder();

            // Try to get the file.
            $this->loadFile();

            // Get the CSV file from the zip
            $this->unzipFile();

            // Check if we have to update the database.
            if (!$this->blnForceUpdate && $this->isDatabaseUpToDate())
            {
                $this->addLog('Geodata updated successfully. Already up to date.', __CLASS__ . '::' . __FUNCTION__, TL_GENERAL);
                $this->sendEmail(true, true, '');
                return true;
            }

            // Id dry run end here.
            if ($this->blnDryRun)
            {
                $this->addLog('Download file successfully, end of dry-run.', __CLASS__ . '::' . __FUNCTION__, TL_GENERAL);
                return true;
            }

            // Read the csv and import it.
            $this->readCSV();

            // Update the localconfig.
            $this->updateSettings();
        }
        catch (\Exception $exc)
        {
            $this->addLog($exc->getMessage(), __CLASS__ . '::' . __FUNCTION__ . '(' . $exc->getLine() . ')', TL_ERROR);
            $this->sendEmail(false, false, $exc->getMessage());
            return false;
        }

        $this->addLog('Geodata updated successfully. Add ' . $this->intTotalInsert . ' lines.', __CLASS__ . '::' . __FUNCTION__, TL_GENERAL);
        $this->sendEmail(true, false, '');
        return true;
    }

    /**
     * Check the download folder and create it if not exists.
     */
    protected function checkDownloadFolder()
    {
        $objFolder = new \Folder($this->strDownloadFolder);
        $objFolder->protect();
    }

    /**
     * Create Folder and load from maxmind the zip file
     *
     * @throws \RuntimeException
     *
     * @return boolean True => Get the file.
     */
    protected function loadFile()
    {
        // Build the download path.
        $strFullDownloadUrl = '';
        $strFullDownloadUrl .= $this->strDownloadUrl;

        if (!empty($this->strLicenseKey))
        {
            if (stripos($strFullDownloadUrl, '?') !== false)
            {
                $strFullDownloadUrl .= '&license_key=' . $this->strLicenseKey;
            }
            else
            {
                $strFullDownloadUrl .= '?license_key=' . $this->strLicenseKey;
            }
        }

        // Build folder path.
        $strFolderPath     = $this->strDownloadFolder . DIRECTORY_SEPARATOR . $this->strNameOfTempZipFile;
        $strFullFolderPath = TL_ROOT . DIRECTORY_SEPARATOR . $strFolderPath;

        // Get the file.
        $blnDone = copy($strFullDownloadUrl, $strFullFolderPath);

        // Check if we get the file.
        if (!$blnDone)
        {
            throw new \RuntimeException('Could not download the data from "' . $strFullDownloadUrl . '" to "' . $strFolderPath . '"');
        }
    }

    /**
     * Get the csv file from zip
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function unzipFile()
    {
        // Open the zip.
        $objZipArchive = new \ZipReader($this->strDownloadFolder . DIRECTORY_SEPARATOR . $this->strNameOfTempZipFile);

        // Search for the csv file.
        foreach ($objZipArchive->getFileList() as $value)
        {
            // Search csv.
            if (preg_match("/GeoIP-103_\d*\.csv/i", $value) || preg_match("/GeoIPCountryWhois.csv/i", $value))
            {
                // If we found this file we have the free version. So don't update the cache.
                if (preg_match("/GeoIPCountryWhois.csv/i", $value))
                {
                    $this->blnNoUpdateLastFile = true;
                }

                // Get the real filename
                $this->strRealNameOfCsv = basename($value);

                // Check if we have a result.
                if (empty($this->strRealNameOfCsv))
                {
                    throw new \RuntimeException('Could not find the real csv name.');
                }

                // Set pointer.
                $objZipArchive->getFile($value);

                // Write file.
                $objFile = new \File($this->strDownloadFolder . DIRECTORY_SEPARATOR . $this->strNameOfTempCsvFile);
                $objFile->write($objZipArchive->unzip());
                $objFile->close();

                // Clear memory.
                unset($objFile);
                unset($objZipArchive);

                return;
            }
        }

        throw new \RuntimeException("Could not find csv file in zip archive.");
    }

    /**
     * Implement the commands to run by this batch program
     */
    protected function readCSV()
    {
        $arrValues = array();
        $intRow    = 0;
        $intRead   = 0;

        //Empty table
        \Database::getInstance()
            ->prepare('TRUNCATE TABLE `tl_geodata`')
            ->execute();

        $objFile = new \File($this->strDownloadFolder . DIRECTORY_SEPARATOR . $this->strNameOfTempCsvFile);

        while (($arrRow = @fgetcsv($objFile->handle, 4048, ',')) !== false)
        {
            $intRow++;
            $intRead++;

            // Skip the first two rows
            if (in_array($intRow, array(1, 2)))
            {
                continue;
            }

            // Get current data
            $arrCurrentData = array(
                'ip_start'      => $arrRow[0],
                'ip_end'        => $arrRow[1],
                'ipnum_start'   => $arrRow[3],
                'ipnum_end'     => $arrRow[4],
                'country_short' => $arrRow[5],
                'country'       => $arrRow[6]
            );

            // Make a string
            $arrValues[] = '("' . implode('","', $arrCurrentData) . '")';

            // Write all 10000 lines to db.
            if ($intRead > 10000)
            {
                $this->addDataToDatabase($arrValues);
                $intRead = 0;
            }
        }

        // Add the last one.
        if (count($arrValues) != 0)
        {
            $this->addDataToDatabase($arrValues);
        }

        $objFile->close();

        unset($objFile);
    }


    /**
     * Write the data to the database.
     *
     * @param array $arrValues (Pointer) List with entries for the database.
     */
    protected function addDataToDatabase(&$arrValues)
    {
        // Increment the count.
        $this->intTotalInsert += count($arrValues);

        // Build query.
        $query = "INSERT INTO tl_geodata (ip_start, ip_end, ipnum_start, ipnum_end, country_short, country) VALUES ";
        $query .= implode(",", $arrValues);

        // Write to the database.
        \Database::getInstance()->query($query);

        // Reset values
        $arrValues = array();
    }

    /**
     * Update the config with the current csv name
     */
    protected function updateSettings()
    {
        if($this->blnNoUpdateLastFile)
        {
            return;
        }

        // Update the config.
        if (empty($GLOBALS['TL_CONFIG']['maxmind_lastUpdate']))
        {
            \Config::getInstance()->add("\$GLOBALS['TL_CONFIG']['maxmind_lastUpdate']", $this->strRealNameOfCsv);
        }
        else
        {
            \Config::getInstance()->update("\$GLOBALS['TL_CONFIG']['maxmind_lastUpdate']", $this->strRealNameOfCsv);
        }
    }

    /**
     * Send A email with information
     * @ToDo: Add notification center support.
     */
    protected function sendEmail($blnSuccess, $blnUpToDate, $strError)
    {
        // Check if flag is set or a notification ID or notification center is inactive.
        if ($this->blnNoMail || empty($this->intNotificationID) || !MaxMindTable::isNotificationCenterActive())
        {
            return;
        }

        // Get the notification.
        $objNotification = Notification::findByPk($this->intNotificationID);
        if ($objNotification == null)
        {
            throw new \RuntimeException('No email template found.');
        }

        $arrTokens = array(
            'new_rows'     => $this->intTotalInsert,
            'websiteTitle' => $GLOBALS['TL_CONFIG']['websiteTitle'],
            'domain'       => \Environment::get('base'),
            'error'        => $GLOBALS['TL_LANG']['maxmind']['none'],
            'state'        => $GLOBALS['TL_LANG']['maxmind']['none'],
            'work'         => $GLOBALS['TL_LANG']['maxmind']['none'],
            'servername'   => $GLOBALS['TL_CONFIG']['maxmind_serverName']
        );

        // Success, new data
        if ($blnSuccess && !$blnUpToDate)
        {
            $arrTokens['state'] = $GLOBALS['TL_LANG']['maxmind']['state_successfully'];
            $arrTokens['work']  = $GLOBALS['TL_LANG']['maxmind']['work_full'];
        }
        // Success, no new data
        else if ($blnSuccess && $blnUpToDate)
        {
            $arrTokens['state'] = $GLOBALS['TL_LANG']['maxmind']['state_successfully'];
            $arrTokens['work']  = $GLOBALS['TL_LANG']['maxmind']['work_update_to_date'];
        }
        // Error
        else if (!$blnSuccess)
        {
            $arrTokens['state'] = $GLOBALS['TL_LANG']['maxmind']['state_failed'];
            $arrTokens['error'] = $strError;
        }
        // Unknown
        else
        {
            $arrTokens['state'] = $GLOBALS['TL_LANG']['maxmind']['state_unknown'];
            $arrTokens['error'] = $strError;
        }

        $objNotification->send($arrTokens, $GLOBALS['TL_LANGUAGE']);
    }


}