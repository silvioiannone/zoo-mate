<?php

namespace SI\Joomla\ZOO\Elements;

/**
 * ZoolandersRelatedItemsPro element.
 *
 * @package Bloom\ZOO\Elements
 */
class ZoolandersRelatedItemsPro extends AbstractElement
{
    protected $structure = [];

    /**
     * Use this method to define how the data should be stored inside the element.
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): ZoolandersRelatedItemsPro
    {
        $this->structure = array_merge($this->structure, $data);

        return $this;
    }
}