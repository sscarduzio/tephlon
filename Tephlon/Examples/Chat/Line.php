<?php
class Line {
	public function __construct($text = null){
		$this->time = time();
		$this->text = $text;
	}
    public $time;
    public $text;
}