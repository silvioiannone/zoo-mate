<?php

namespace SI\Joomla\ZOO\Elements;

/**
 * This class represents a ZOO Item Element. An element is stored inside `elements` Item property
 * and together with it its data is also stored.
 *
 * @package Bloom\ZOO\Elements
 */
abstract class AbstractElement
{
    /**
     * Holds the element structure.
     *
     * @var array
     */
    protected $structure;

    /**
     * Get the element data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->structure;
    }

    /**
     * Use this method to define how the data should be stored inside the element.
     *
     * @param array $data
     * @return self
     */
    abstract public function setData(array $data);
}