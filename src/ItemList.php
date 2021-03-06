<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 * @package BoxPacker
 * @author Doug Wright
 */
declare(strict_types=1);
namespace DVDoug\BoxPacker;

use ArrayIterator, Countable, IteratorAggregate, Traversable;

/**
 * List of items to be packed, ordered by volume
 * @author Doug Wright
 * @package BoxPacker
 */
class ItemList implements Countable, IteratorAggregate
{
    /**
     * List containing items
     * @var Item[]
     */
    private $list = [];

    /**
     * Has this list already been sorted?
     * @var bool
     */
    private $isSorted = false;

    /**
     * @param Item $item
     */
    public function insert(Item $item)
    {
        $this->list[] = $item;
    }

    /**
     * @internal
     * @return Item
     */
    public function extract(): Item
    {
        if (!$this->isSorted) {
            usort($this->list, [$this, 'compare']);
            $this->isSorted = true;
        }
        return array_shift($this->list);
    }

    /**
     * @internal
     * @return Item
     */
    public function top(): Item
    {
        if (!$this->isSorted) {
            usort($this->list, [$this, 'compare']);
            $this->isSorted = true;
        }
        $temp = $this->list;
        return reset($temp);
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        if (!$this->isSorted) {
            usort($this->list, [$this, 'compare']);
            $this->isSorted = true;
        }
        return new ArrayIterator($this->list);
    }

    /**
     * Number of items in list
     * @return int
     */
    public function count(): int
    {
        return count($this->list);
    }

    /**
     * @param Item $itemA
     * @param Item $itemB
     *
     * @return int
     */
    private function compare(Item $itemA, Item $itemB): int
    {
        $itemAVolume = $itemA->getWidth() * $itemA->getLength() * $itemA->getDepth();
        $itemBVolume = $itemB->getWidth() * $itemB->getLength() * $itemB->getDepth();
        $volumeDecider = $itemBVolume <=> $itemAVolume;
        $weightDecider = $itemB->getWeight() - $itemA->getWeight();
        if ($volumeDecider !== 0) {
            return $volumeDecider;
        } elseif ($weightDecider !== 0) {
            return $weightDecider;
        } else {
            return $itemA->getDescription() <=> $itemB->getDescription();
        }
    }

}
