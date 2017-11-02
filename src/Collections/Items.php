<?php

namespace SI\Joomla\ZOO\Collections;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use SI\API\Mspecs\Collection;
use SI\Joomla\ZOO\Item;

/**
 * A collection of ZOO items.
 */
class Items
{
    /**
     * Database connection.
     *
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * Items constructor.
     *
     * @param ConnectionInterface $connection This interface is provided by
     *        https://github.com/illuminate/database so by using that package you're set to go.
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->db = $connection;
    }

    /**
     * Search the ZOO items.
     *
     * @param string[] $elementIds
     * @param string $value
     * @return Item[]
     */
    public function search(array $elementIds, string $value): array
    {
        $itemIds = $this->db->table('zoo_search_index')
            ->whereIn('element_id', $elementIds)
            ->where('value', 'like', '%' . $value . '%')
            ->get()
            ->pluck('item_id')
            ->toArray();

        return array_map(function ($itemId)
        {
            return (new Item($this->db))->find($itemId);
        }, $itemIds);
    }

    /**
     * Directly query the database and return items.
     *
     * @param callable $callback Receives a `Illuminate\Database\Query\Builder` as a parameter and
     *                           it must return a `Illuminate\Support\Collection`
     * @return array
     */
    public function query(callable $callback): array
    {
        $lastItemId = null;
        $itemsLeft = true;

        // Chunk the DB requests in case thare are a lot of items.
        $builder = $this->db->table('zoo_item')
            ->limit(100)
            ->orderBy('id');

        $collectedItems = [];

        while ($itemsLeft) {

            /** @var Builder $builder */
            $builder = $callback($builder);
            if ($lastItemId) {
                $builder = $builder->where('id', '>', $lastItemId);
            }

            $collectedItems[] = $builder->get()->toArray();

            if ($builder->get()->count()) {
                $lastItemId = $builder->get()->last()->id;
                $itemsLeft = (bool) $builder->count();
            } else {
                $itemsLeft = 0;
            }
        }

        return array_merge_recursive(...$collectedItems);
    }
}
