<?php
require_once( BASE_PATH . "/lib/log/log4php/Logger.php");

$log = Logger::getRootLogger();
$log->setLevel(LoggerLevel::toLevel(AE_LOG_LEVEL));

$appender = new LoggerAppenderRollingFile("MyAppender");
$appender->setFile( BASE_PATH . "/log/tephlon.log", true);
$appender->setMaxBackupIndex(10);
$appender->setMaxFileSize("10MB");
$appenderlayout = new LoggerLayoutPattern();
$pattern = '%d{d.m.Y H.i.s:u} [%p] %t: %m (at %M)%n';
//$pattern = '%d{d.m.Y H.i.s:u} [%p] %m %n';
$appenderlayout->setConversionPattern($pattern);
$appender->setLayout($appenderlayout);
$appender->activateOptions();

$log->removeAllAppenders();
$log->addAppender($appender);
$log->info(" *** Engine initializing ***");

function getLogger() {
	global $log;
	return $log;
}

function initLogger(){
	$loggerName = "log";
	// Iterate over all declared classes
	$classes = get_declared_classes();
	foreach( $classes as $class ) {
		$reflection = new ReflectionClass( $class );

		// If the class is internally defined by PHP or has no property called "logger", skip it.
		if( $reflection->isInternal() || !$reflection->hasProperty( $loggerName ) ) continue;

		// Get information regarding the "logger" property of this class.
		$property = new ReflectionProperty( $class, $loggerName );

		// If the "logger" property is not static or not public, then it is not the one we are interested in. Skip this class.
		if( !$property->isStatic() || !$property->isPublic() ) continue;

		// Initialize the logger for this class.
		$reflection->setStaticPropertyValue( $loggerName, getLogger());
	}

}