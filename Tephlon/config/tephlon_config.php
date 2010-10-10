<?php
/* ************** Tephlon library configuration ***************************** */

// The default lifetime of a record, 0 = forever
define("DEFAULT_STALE_AGE", 0);

// Verbosity of logging. Values from more to less verbose: {DEBUG, INFO, ERROR}
//define("LOG_VERBOSITY", INFO);
define("LOG_VERBOSITY", ERROR);



/* ************************ File Driver ************************************* */
// Resources are directories, Records are files containing your objects/vars
// In which dir to store them? You may want to put absolute path for clarity.
define("FILE_CACHE_DIR", "/cache/");


/* ************************ SQL Driver ************************************ */
define("sqlDrv1".CONN_STR, "mysql://root:@localhost/tephlon");
//define("sqlDrv2".CONN_STR, "oci8://user:pwd@tnsname/?charset=WE8MSWIN1252");