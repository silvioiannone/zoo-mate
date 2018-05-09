<?php

namespace SI\Joomla\ZOO;

use SI\Joomla\ZOO\Exceptions\ItemNotFound;
use Si\Joomla\ZOO\Exceptions\MissingItemProperty;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;

/**
 * This class represents a zoo item that is usually stored in the 'zoo_item' table of a Joomla
 * website.
 *
 * @package Bloom\ZOO
 */
class Item
{
    /**
     * @var int
     */
    public $access;

    /**
     * @var string Alias.
     */
    public $alias;

    /**
     * @var int Item application ID.
     */
    public $applicationId;

    /**
     * @var Carbon Created date.
     */
    public $created;

    /**
     * @var
     */
    public $createdBy;

    /**
     * @var
     */
    public $createdByAlias;

    /**
     * @var \stdClass ZOO item elements.
     */
    public $elements;

    /**
     * @var int Number of views.
     */
    public $hits;

    /**
     * @var int Item ID.\
     */
    public $id;

    /**
     * @var Carbon Modified date.
     */
    public $modified;

    /**
     * @var int
     */
    public $modifiedBy;

    /**
     * @var string Item name.
     */
    public $name;

    /**
     * @var
     */
    public $params = [];

    /**
     * @var int Priority.
     */
    public $priority;

    /**
     * @var Carbon Unpublished date.
     */
    public $publishDown;

    /**
     * @var Carbon Published date.
     */
    public $publishUp;

    /**
     * @var
     */
    public $searchable;

    /**
     * @var bool Wheter it's published or not.
     */
    public $state;

    /**
     * @var string Item type.
     */
    public $type;

    /**
     * Connection to the DB where the ZOO item is stored.
     *
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * Item constructor.
     *
     * @param ConnectionInterface $dbConnection This interface is provided by
     *        https://github.com/illuminate/database so by using that package you're set to go.
     */
    public function __construct(ConnectionInterface $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Attach a category to the Item.
     *
     * @param Category $category
     * @return Item
     */
    public function attachCategory(Category $category): self
    {
        $this->db->table('zoo_category_item')->updateOrInsert([
            'category_id' => $category->id,
            'item_id' => $this->id
        ], [
            'category_id' => $category->id,
            'item_id' => $this->id
        ]);

        return $this;
    }

    /**
     * Detach a category.
     *
     * @param Category $category
     * @return Item
     */
    public function detachCategory(Category $category): self
    {
        $this->db->table('zoo_category_item')
            ->where('category_id', $category->id)
            ->where('item_id', $this->id)
            ->delete();

        return $this;
    }

    /**
     * Get the attached categories.
     *
     * @return array
     */
    public function categories(): array
    {
        $categoryIds = $this->db->table('zoo_category_item')
            ->where('item_id', $this->id)
            ->get()
            ->pluck('category_id')
            ->toArray();

        return array_map(function($categoryId)
        {
            return (new Category($this->db))->find($categoryId);
        }, $categoryIds);
    }

    /**
     * Get the attached tags.
     *
     * @return Tag[]
     */
    public function tags(): array
    {
        $rawTags = $this->db->table('zoo_tag')
            ->where('item_id', $this->id)
            ->get()
            ->toArray();

        return array_map(function(\stdClass $rawTag)
        {
            return (new Tag($this->db))->set([
                'itemId' => $rawTag->item_id,
                'name' => $rawTag->name
            ]);
        }, $rawTags);
    }

    /**
     * Load an item from the database using its id.
     *
     * @param integer $itemId
     * @return Item
     * @throws \SI\Joomla\ZOO\Exceptions\ItemNotFound
     */
    public function find(int $itemId): self
    {
        $this->id = $itemId;

        $this->load();

        return $this;
    }

    /**
     * Delete the ZOO item.
     *
     * @return Item
     */
    public function delete(): self
    {
        $this->db->table('zoo_item')
            ->where('id', $this->id)
            ->delete();

        return $this;
    }

    /**
     * Link the item to another item.
     *
     * @param Item $item
     * @param string $element
     */
    public function linkTo(Item $item, string $element)
    {
        $relationData = [
            'item_id' => $this->id,
            'ritem_id' => $item->id,
            'element_id' => $element,
            'params' => ''
        ];

        $this->db->table('zoo_relateditemsproxref')
            ->updateOrInsert($relationData, $relationData);
    }

    /**
     * Remove all the relations.
     */
    public function unlinkAll(): self
    {
        $this->db->table('zoo_relateditemsproxref')
            ->where('item_id', $this->id)
            ->delete();

        return $this;
    }

    /**
     * Save the item in the database.
     *
     * @return Item
     */
    public function save(): self
    {
        // If the ID is not set...
        if(!$this->id)
        {
            // ...create a new item...
            $this->id = $this->db->table('zoo_item')
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
     * Set the properties of the ZOO item.
     *
     * @param array $data An array containing the data.
     * @return Item
     * @throws MissingItemProperty
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
                throw new MissingItemProperty($key);
            }
        }

        return $this;
    }

    /**
     * Load the Item from the DB.
     *
     * @throws \SI\Joomla\ZOO\Exceptions\ItemNotFound
     */
    protected function load()
    {
        $rawItem = $this->db->table('zoo_item')->find($this->id);

        if(!$rawItem)
        {
            throw new ItemNotFound($this->id);
        }

        $this->access = $rawItem->access;
        $this->alias = $rawItem->alias;
        $this->applicationId = $rawItem->application_id;
        $this->created = new Carbon($rawItem->created);
        $this->createdBy = $rawItem->created_by;
        $this->createdByAlias = $rawItem->created_by_alias;
        $this->elements = json_decode($rawItem->elements, true);
        $this->hits = $rawItem->hits;
        $this->modified = new Carbon($rawItem->modified);
        $this->name = $rawItem->name;
        $this->params = json_decode($rawItem->params, true);
        $this->priority = $rawItem->priority;
        $this->publishDown = new Carbon($rawItem->publish_down);
        $this->publishUp = new Carbon($rawItem->publish_up);
        $this->searchable = $rawItem->searchable;
        $this->state = $rawItem->state;
        $this->type = $rawItem->type;
    }

    /**
     * Transform the Item properties to an array.
     *
     * @return array
     */
    protected function toRawItem(): array
    {
        $publishDown = '2030-04-11';
        $publishUp = Carbon::now()->toDateTimeString();

        if ($this->publishDown) {
            $publishDown = $this->publishDown->year < 1 ?: $this->publishDown->toDateTimeString();
        }

        if ($this->publishUp) {
            $publishUp = $this->publishUp->year < 1 ?: $this->publishUp->toDateTimeString();
        }

        $rawItem = [
            'access' => $this->access ?? 1,
            'alias' => $this->alias,
            'application_id' => $this->applicationId,
            'created' => $this->created ?
                $this->created->toDateTimeString() : Carbon::now(),
            'created_by' => $this->createdBy ?? 0,
            'created_by_alias' => $this->createdByAlias ?? 'Bloom\ZOO\Item',
            'elements' => json_encode($this->elements ?? [], JSON_PRETTY_PRINT),
            'hits' => $this->hits ?? 0,
            'modified' => $this->modified ?
                $this->modified->toDateTimeString() : Carbon::now(),
            'modified_by' => $this->modifiedBy ?? 0,
            'name' => $this->name,
            'params' => json_encode($this->params ?? [], JSON_PRETTY_PRINT),
            'priority' => $this->priority ?? 0,
            'publish_down' => $publishDown,
            'publish_up' => $publishUp,
            'searchable' => $this->searchable ?? 1,
            'state' => $this->state ?? 0,
            'type' => $this->type ?? 0
        ];

        return $rawItem;
    }

    /**
     * Transform the item to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = array_merge(['id' => $this->id], $this->toRawItem());
        $array['elements'] = json_decode($array['elements']);

        return $array;
    }
}
