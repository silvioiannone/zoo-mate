<?php

namespace SI\Joomla\ZOO\Elements;

/**
 * ZoolandersGoogleMapsPro element.
 *
 * @package Bloom\ZOO\Elements
 */
class ZoolandersGoogleMapsPro extends AbstractElement
{
    protected $structure = [
        'location' => '',
        'location_name' => '',
        'address' => ''
    ];

    /**
     * Use this method to define how the data should be stored inside the element.
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): ZoolandersGoogleMapsPro
    {
        $this->structure = array_merge($this->structure, $data);

        return $this;
    }
}