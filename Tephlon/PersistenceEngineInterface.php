<?php
/**
 * Interface to PersistenceEngine
 * 
 * PersistenceEngine is the superclass that exports the 
 * only public methods that will be visible to the developer.
 * 
 * Main Principles:
 * 1. Developer should never come in contact with Record object
 * because this is an abstraction proper of the PersitenceEngine.
 * 
 * 2. Only few and versatile methods should be public: get() and 
 */

interface PersistenceEngineInterface {
	
	/**
	 * Singleton's instance accessor
	 */
	public static function getInstance();
	
	/**
	 * 
	 */
	public static function getStaleAge();
	
	/**
	 * Set default lifetime for a record
	 *
	 * @param unknown_type $time
	 * @return void
	 */
	public static function setStaleAge($time);
	
	/**
	 * 
	 */
 	public function register($object, $label);
 	
 	/**
 	 * 
 	 */
 	public function retrieve($label, $default=null);
 	
 	/**
 	 * 
 	 */
 	public function delete($label);
}