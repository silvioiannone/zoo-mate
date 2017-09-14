<?php

namespace SI\Joomla\ZOO\Exceptions;

use Exception;

/**
 * This exception is thrown whenever an Item property cannot be found.
 *
 * @package Bloom\ZOO\Exceptions
 */
class MissingItemProperty extends \Exception
{
    /**
     * MissingItemProperty constructor.
     *
     * @param string $property
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $property, $code = 0, Exception $previous = null)
    {
        $message = 'The property ' . $property . ' could not be found on the item.';

        parent::__construct($message, $code, $previous);
    }
}