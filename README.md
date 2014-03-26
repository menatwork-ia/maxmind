MaxMind Extension
=================

Die MaxMind Extension ist als genereller Cronjob für die Übertragung von GeoIP Daten gedacht. Mit Hilfe der Backendkonfiguration kann eine ZIP-Datei automatisiert heruntergeladen und verarbeitet werden. Auch kostenpflichtige ZIP-Dateien können mit einem optionalen Lizenzkey verarbeitet werden.

The MaxMind extension is intended as a general cronjob for the transmission of GeoIP data. Using the backend configuration, a zip file will be downloaded automatically and processed. Also paid ZIP files can be processed with an optional license key.


### Konsole / Console

```
cd /var/www/contao/system/modules/maxmind
php MaxMindCaller.php
```


### HTTP Aufruf / HTTP Request

Da Contao 3.x sämtliche Ordner in system/modules schützt, ist ein direkter Aufruf ohne Anpassungen (wie z.B. Änderung der .htaccess im Root oder dem Ablegen einer .htaccess in system/modules) nur in Contao 2.11 möglich.

As Contao 3+ protects all subfolders within system/modules, direct requests into there are possible only in Contao 2.11 without adjustments (such as a change in the .htaccess of the root or adding an own .htaccess within a designated folder to be whitelisted).

http://www.example.com/system/modules/cleanup/MaxMindCaller.php


### Contao Cronjob

Contao bietet die Möglichkeit sich in die systemeigenen Cronjobs zu integrieren. Dafür muss man nur eins der 5 möglichen Beispiele aus der config.example.php in die dcaconfig.php oder in die config.php der eigenen Extension übernehmen und einkommentieren. Der stündliche und minütige Aufruf ist in Contao 2.11 nicht vorhanden.

Contao provides the ability to integrate own requests into the native system cron jobs. Therefore you have to take over one of the following 5 examples from the config.example.php and paste them into the dcaconfig.php or config.php of your own extension. The hourly and minute request is not available in Contao 2.11.

```php
$GLOBALS['TL_CRON']['monthly'][]    = array('MaxMind\MaxMind', 'run');
$GLOBALS['TL_CRON']['weekly'][]     = array('MaxMind\MaxMind', 'run');
$GLOBALS['TL_CRON']['daily'][]      = array('MaxMind\MaxMind', 'run');

// Contao 3 only
$GLOBALS['TL_CRON']['hourly'][]     = array('MaxMind\MaxMind', 'run');
$GLOBALS['TL_CRON']['minutely'][]   = array('MaxMind\MaxMind', 'run');
```