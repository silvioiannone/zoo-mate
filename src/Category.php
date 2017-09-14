<?php

namespace SI\Joomla\ZOO;

use Bloom\ZOO\Exceptions\MissingCategoryProperty;
use Illuminate\Database\ConnectionInterface;

/**
 * This class represents a ZOO Category.
 *
 * @package Bloom\ZOO
 */
class Category
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $applicationId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $alias;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $parent;

    /**
     * @var int
     */
    public $ordering;

    /**
     * @var bool
     */
    public $published;

    /**
     * @var array
     */
    public $params;

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
     * Find a category.
     *
     * @param int $id
     * @return Category
     */
    public function find(int $id): self
    {
        $this->id = $id;

        return $this->load();
    }

    /**
     * Find a category using its alias.
     *
     * @param string $alias
     * @return Category
     */
    public function findAlias(string $alias): self
    {
        if(!$this->db->table('zoo_category')->where('alias', $alias)->count())
        {
            return $this;
        }

        $this->id = $this->db->table('zoo_category')->where('alias', $alias)->first()->id;

        return $this->load();
    }

    /**
     * Find or create from alias.
     *
     * @param string $alias
     * @return Category
     */
    public function findOrCreateFromAlias(string $alias): self
    {
        $this->findAlias($alias);

        if ($this->id) return $this;

        $this->alias = $alias;
        $this->save();

        return $this;
    }

    /**
     * Save the category in the database.
     *
     * @return Category
     */
    public function save(): self
    {
        // If the ID is not set...
        if(!$this->id)
        {
            // ...create a new item...
            $this->id = $this->db->table('zoo_category')
                ->insertGetId($this->toRawItem());

            $this->load();
        }
        else
        {
            // ...or update the existing one.
            $this->db->table('zoo_item')
                ->where('id', $this->id)
                ->update($this->toRawItem());
        }

        return $this;
    }

    /**
     * Set the category data.
     *
     * @param array $data
     * @return Category
     * @throws MissingCategoryProperty
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
                throw new MissingCategoryProperty($key);
            }
        }

        return $this;
    }

    /**
     * Load the category from the DB.
     *
     * @return Category
     */
    protected function load(): self
    {
        $rawCategory = $this->db->table('zoo_category')->find($this->id);

        $this->applicationId = $rawCategory->application_id;
        $this->name = $rawCategory->name;
        $this->alias = $rawCategory->alias;
        $this->description = $rawCategory->description;
        $this->parent = $rawCategory->parent;
        $this->ordering = $rawCategory->ordering;
        $this->published = $rawCategory->published;
        $this->params = json_decode($rawCategory->params, true);

        return $this;
    }

    /**
     * Transform the Category fields into an array
     *
     * @return array
     */
    protected function toRawItem(): array
    {
        $rawCategory = [
            'application_id' => $this->applicationId,
            'name' => $this->name ?? $this->alias,
            'alias' => $this->alias,
            'description' => $this->description ?? '',
            'parent' => $this->parent ?? 0,
            'ordering' => $this->ordering ?? 0,
            'published' => $this->published ?? true,
            'params' => json_encode($this->params, JSON_PRETTY_PRINT)
        ];

        return $rawCategory;
    }
}