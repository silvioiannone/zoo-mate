<?php

namespace SI\Joomla\ZOO;

use Bloom\ZOO\Exceptions\MissingTagProperty;
use Illuminate\Database\ConnectionInterface;

/**
 * This class represents a ZOO item tag.
 *
 * @package Bloom\ZOO
 */
class Tag
{
    /**
     * Item ID.
     *
     * @var integer
     */
    protected $itemId;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Database connection.
     *
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * Category constructor.
     *
     * @param ConnectionInterface $dbConnection This interface is provided by
     *        https://github.com/illuminate/database so by using that package you're set to go.
     */
    public function __construct(ConnectionInterface $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Set the tag data.
     *
     * @param array $data
     * @return Tag
     * @throws MissingTagProperty
     */
    public function set(array $data): self
    {
        foreach ($data as $key => $value)
        {
            try
            {
                $this->{$key} = $value;
            }
            catch (\Exception $e)
            {
                throw new MissingTagProperty($key);
            }
        }

        return $this;
    }

    /**
     * Save the tag.
     *
     * @return Tag
     */
    public function save(): self
    {
        $alreadyExisting = $this->db->table('zoo_tag')
            ->where('item_id', $this->itemId)
            ->where('name', $this->name)
            ->count();

        if ($alreadyExisting)
        {
            return $this;
        }

        $this->db->table('zoo_tag')
            ->insert([
                'item_id' => $this->itemId,
                'name' => $this->name
            ]);

        return $this;
    }

    /**
     * Delete the tag.
     *
     * @return void
     */
    public function delete(): void
    {
        $this->db->table('zoo_tag')
            ->where('item_id', $this->itemId)
            ->where('name', $this->name)
            ->delete();
    }

    /**
     * Load the tag from the DB.
     *
     * @return Tag
     */
    protected function load(): self
    {
        $rawTag = $this->db->table('zoo_tag')
            ->where('itemId', $this->itemId)
            ->where('name', $this->name)
            ->first();

        $this->itemId = $rawTag['item_id'];
        $this->name = $rawTag['name'];

        return $this;
    }
}
