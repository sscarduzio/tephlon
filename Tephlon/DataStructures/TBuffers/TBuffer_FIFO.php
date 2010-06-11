<?php
/**
 * Implementation of a FIFO buffer
 * Adding an object with add() method will result in the deletion
 * of the oldest record if the size of the buffer is exceeded.
 * Regular deletion and edit is not supported.
 *
 */

require_once(dirname(__FILE__).'/AbstractTBuffer.php');

class TBuffer_FIFO extends AbstractTBuffer{

	/**
	 * FIFO specific algorithm for choosing which is the next element to serve
	 *
	 * @param $idx sorted array
	 */
	protected function nextLabel($idx){
		return  $idx[0];
	}

}