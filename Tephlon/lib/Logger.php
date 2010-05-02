<?php
class Logger {
	private $verbosisty;
	private static $inst = null;

	private function __construct(){
		if(is_int(LOG_VERBOSITY) && LOG_VERBOSITY > 0 && LOG_VERBOSITY < 4){
			$this->verbosisty = LOG_VERBOSITY;
			date_default_timezone_set("EET");
		}

		else die("LOG_VERBOSITY constant must be initialized correctly in conf/tephlon_conf.php.");
	}

	public static function getInstance(){
		if(!self::$inst){
			self::$inst = new Logger();
		}
		return self::$inst;
	}


	function dlog($msg, $logLevel = 2){
		$threshold = $this->getVerbosity();
		// If 'importance' of this log message is bigger than the threshold, print
		if($logLevel <= $threshold){
			switch ($logLevel){
				case ERROR: $type = "ERR"; break;
				case DEBUG: $type = "DBG"; break;
				case INFO: $type = "INF";
			}
			$ut = explode(".",microtime(true));
			$ut =$this->rpadder($ut[1], 'j', 3);

			echo "".date("h:i:s.").$ut." [$type] ".$msg."\n";
			if($this->getVerbosity() > 1 && $logLevel == ERROR){
                debug_print_backtrace();				
			}
		}
	}


	// Pad or truncate, force $str to return $len chars.
	private function rpadder($str, $pad, $len) {
		$delta =  $len - strlen($str);
		if($delta < 0){
			return substr($str, 0, $len);
		}
		for($i = 0; $i < $delta; $i++){
			$str = $str.'0';
		}
		return $str;
	}
	public function setVerbosity($v){
		if(!is_int($this->verbosisty)){
			die("Can't log, verbosity level was not initialized!");
		}
		if(!is_int($v)){
			dlog("Verbosity value must be int, given: $v", ERROR);
			return false;
		}
		if( !(1 <= $v && $v <= 3)){
			dlog("Verbosity value out of range! Must be between 1 and 3, given: $v", ERROR);
			return false;
		}
		if( $v == $this->verbosisty){
			dlog("Not changing verbosity level, it was already $v", INFO);
		}
		$this->verbosisty = $v;
		return true;

	}
	public function getVerbosity(){
		return $this->verbosisty;
	}
}