<?php


$log = Logger::getRootLogger();
$log->setLevel(LoggerLevel::toLevel(AE_LOG_LEVEL));

$appender = new LoggerAppenderRollingFile("MyAppender");
$appender->setFile(LOGFILE_ENGINE, true);
$appender->setMaxBackupIndex(10); 
$appender->setMaxFileSize("10MB");
$appenderlayout = new LoggerLayoutPattern();
//$pattern = '%d{Y.m.d H:i:s} [%p] %c: %m (at %F line %L)%n';
$pattern = '%d{d.m.Y H.i.s:u} [%p] %m %n';
$appenderlayout->setConversionPattern($pattern);
$appender->setLayout($appenderlayout);
$appender->activateOptions();

$loggr->removeAllAppenders();
$log->addAppender($appender);

$log->info(" *** Engine initializing ***");

/**
 * CDR Logger
 */
$cdrLogger = Logger::getLogger("CDR");
$appender = new LoggerAppenderRollingFile("CDRAppender");
$appender->setFile(LOGFILE_CDR, true);
$appender->setMaxBackupIndex(10);
$appender->setMaxFileSize("100MB");
$appenderlayout = new LoggerLayoutPattern();
$pattern = '%d{d.m.Y H.i.s:u};%m %n';
$appenderlayout->setConversionPattern($pattern);
$appender->setLayout($appenderlayout);
$appender->activateOptions();

$cdrLogger->removeAllAppenders();
$cdrLogger->addAppender($appender);

function writeCDR($str){
	global $cdrLogger;
	$cdrLogger->info($str);
}