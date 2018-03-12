<?php

namespace SI\Joomla\ZOO\Elements;

/**
 * ZoolandersRelatedCategoriesPro element.
 *
 * @package Bloom\ZOO\Elements
 */
class ZoolandersRelatedCategoriesPro extends AbstractElement
{
    protected $structure = [
        'category' => [
            '0' => 0
        ]
    ];

    /**
     * Use this method to define how the data should be stored inside the element.
     *
     * @param array $data
     * @return ZoolandersRelatedCategoriesPro
     */
    public function setData(array $data): ZoolandersRelatedCategoriesPro
    {
        $this->structure = $data;

        return $this;
    }
}
