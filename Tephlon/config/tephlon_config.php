<?php
/* ************** Tephlon library configuration ***************************** */

/* On production system the Error level is recommended.
 * Possible values are:
 *
 * LoggerLevel::ALL
 * LoggerLevel::DEBUG
 * LoggerLevel::ERROR 
 */
 define("AE_LOG_LEVEL", LoggerLevel::ALL);

/*
 * Timezone settings, refer to: http://php.net/manual/en/timezones.php
 */
 date_default_timezone_set("Europe/Helsinki");

 // The default lifetime of a record, 0 = forever
 define("DEFAULT_STALE_AGE", 0);


/* ************************ File Driver ************************************* */
// Resources are directories, Records are files containing your objects/vars
// In which dir to store them? You may want to put absolute path for clarity.
define("FILE_CACHE_DIR", "/cache/");


/* ************************ SQL Driver ************************************ */
define("sqlDrv1".CONN_STR, "mysql://root:@localhost/tephlon");
//define("sqlDrv2".CONN_STR, "oci8://user:pwd@tnsname/?charset=WE8MSWIN1252");