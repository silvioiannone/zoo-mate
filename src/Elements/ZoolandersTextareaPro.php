<?php

namespace SI\Joomla\ZOO\Elements;

/**
 * ZoolandersTextareaPro element.
 *
 * @package Bloom\ZOO\Elements
 */
class ZoolandersTextareaPro extends AbstractElement
{
    protected $structure = [
        [
            'value' => ''
        ]
    ];

    /**
     * Use this method to define how the data should be stored inside the element.
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): ZoolandersTextareaPro
    {
        $this->structure = $data;

        return $this;
    }
}