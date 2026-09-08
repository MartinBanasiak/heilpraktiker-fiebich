<?php
/**
 * Interface fuer die Worker
 * 
 */
interface WorkerInterface {
	
	/**
	 * Interne ID fuer amazon
	 * 
	 * @static
	 * @var int
	 */
	const TYPE_AMAZON = 1;
	
	/**
	 * Interne ID fuer ebay
	 * 
	 * @static
	 * @var int
	 */
	const TYPE_EBAY = 2;
	
	/**
	 * Hauptprogramm
	 */
	public function run();
	
}