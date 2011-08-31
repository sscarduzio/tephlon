<?php
/* ************** Tephlon library configuration ***************************** */

/*
 * Timezone settings, refer to: http://php.net/manual/en/timezones.php
 */
 define("TZONE","Europe/Helsinki");

 /* On production system the Error level is recommended.
 * Possible values are:
 *
 * ALL
 * DEBUG
 * ERROR
 */
 define("AE_LOG_LEVEL", ALL);
 
 // The default lifetime of a record, 0 = forever
 define("DEFAULT_STALE_AGE", 0);


/* ************************ File Driver ************************************* */
// Resources are directories, Records are files containing your objects/vars
// In which dir to store them? You may want to put absolute path for clarity.
define("FILE_CACHE_DIR", "/tmp/cache/");


/* ************************ SQL Drivers ************************************ */
define("sqlDrv1".CONN_STR, "mysql://root:@localhost/tephlon");

define("oraDrv1".CONN_STR, "oci8://user:pwd@tnsname/?charset=WE8MSWIN1252");

$sqliteDbPath = 'sqlite:///tmp/tephlon.db';
define("sqliteDrv1".CONN_STR, $sqliteDbPath);