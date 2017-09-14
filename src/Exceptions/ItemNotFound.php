<?php

namespace SI\Joomla\ZOO\Exceptions;

use Exception;

/**
 * This exception is thrown whenever an Item cannot be found.
 *
 * @package Bloom\ZOO\Exceptions
 */
class ItemNotFound extends \Exception
{
    /**
     * MissingItemProperty constructor.
     *
     * @param int $id
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(int $id, $code = 0, Exception $previous = null)
    {
        $message = 'The item (ID ' . $id . ') could not be found.';

        parent::__construct($message, $code, $previous);
    }
}