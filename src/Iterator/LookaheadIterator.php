<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Iterator;

/**
 * Class LookaheadIterator
 */
class LookaheadIterator implements \Iterator
{
    /**
     * @var \Traversable
     */
    protected $iterator;

    /**
     * @var int
     */
    protected $key = 0;

    /**
     * @var mixed
     */
    protected $current;

    /**
     * @var bool
     */
    protected $valid;

    /**
     * LookaheadIterator constructor.
     * @param iterable $iterator
     * @throws \InvalidArgumentException
     */
    public function __construct(iterable $iterator)
    {
        $this->iterator = $this->toIterator($iterator);
        $this->current  = $this->iterator->current();
        $this->key      = $this->iterator->key();
        $this->valid    = $this->iterator->valid();

        $this->rewind();
    }

    /**
     * @param iterable $iterator
     * @return \Iterator
     * @throws \InvalidArgumentException
     */
    private function toIterator(iterable $iterator): \Iterator
    {
        switch (true) {
            case $iterator instanceof \Iterator:
                return $iterator;
            case $iterator instanceof \Traversable:
                return new \IteratorIterator($iterator);
            case \is_array($iterator):
                return new \ArrayIterator($iterator);
        }

        throw new \InvalidArgumentException('Unsupported iterator type');
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * @return int|mixed
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Rewind the iterator to the first element.
     * @return void
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
        $this->next();
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next(): void
    {
        $this->valid  = $this->iterator->valid();

        if ($this->valid === false) {
            return;
        }

        $this->key     = $this->iterator->key();
        $this->current = $this->iterator->current();

        $this->iterator->next();
    }

    /**
     * Check if current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->valid;
    }

    /**
     * Check whether there is a next element.
     *
     * @return bool
     */
    public function hasNext(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * Get next value.
     *
     * @return mixed
     */
    public function getNext()
    {
        return $this->iterator->current();
    }

    /**
     * Get next key.
     *
     * @return mixed
     */
    public function getNextKey()
    {
        return $this->iterator->key();
    }
}
