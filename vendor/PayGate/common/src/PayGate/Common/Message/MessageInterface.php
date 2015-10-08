<?php
/**
 * Message Interface
 */

namespace PayGate\Common\Message;

/**
 * Message Interface
 *
 * This interface class defines the standard functions that any PayGate message
 * interface needs to be able to provide.  
 */
interface MessageInterface
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData();
}
