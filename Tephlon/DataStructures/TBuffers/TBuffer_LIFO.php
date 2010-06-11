<?php
/**
 * Implementation of a FIFO buffer
 * Adding an object with add() method will result in the deletion
 * of the oldest record if the size of the buffer is exceeded.
 * Regular deletion and edit is not supported.
 *
 */

require_once('AbstractTBuffer.php');

class TBuffer_LIFO extends AbstractTBuffer{

	/**
	 * LIFO specific algorithm for choosing which is the next element to serve
	 *
	 * @param $idx sorted array
	 */
	protected function nextLabel($idx){
		return $idx[count($idx)-1];
	}

}