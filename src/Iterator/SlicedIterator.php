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
 * Class SlicedIterator
 */
class SlicedIterator implements \Iterator
{
    /**
     * @var array
     */
    private $iterator;

    /**
     * @var int
     */
    private $cursor = 0;

    /**
     * SlicedIterator constructor.
     * @param iterable $iterator
     */
    public function __construct(iterable $iterator)
    {
        $this->iterator = \array_values($this->toArray($iterator));
    }

    /**
     * @param iterable $iterator
     * @return array
     */
    private function toArray(iterable $iterator): array
    {
        return $iterator instanceof \Traversable ? \iterator_to_array($iterator) : $iterator;
    }

    /**
     * @return \Traversable
     */
    public function getOuterIterator(): \Traversable
    {
        $cursor = $this->cursor;

        for ($i = 0; \array_key_exists($cursor, $this->iterator); ++$i) {
            yield $i => $this->iterator[$cursor++];
        }
    }

    /**
     * @param int $offset
     * @return mixed|null
     */
    public function lookahead(int $offset = 0)
    {
        return $this->iterator[$this->cursor + $offset] ?? null;
    }

    /**
     * @param int $offset
     * @return \Traversable|SlicedIterator
     */
    public function slice(int $offset): \Traversable
    {
        $result = \array_splice($this->iterator, $this->cursor, $offset);

        $this->iterator = \array_values($this->iterator);

        return new static($result);
    }

    /**
     * @param \Closure $filter
     * @return \Traversable|SlicedIterator
     */
    public function sliceWhile(\Closure $filter): \Traversable
    {
        foreach ($this->getOuterIterator() as $i => $item) {
            if ($filter($item)) {
                return $this->slice($i);
            }
        }

        return new static([]);
    }

    /**
     * @return mixed|null
     */
    public function current()
    {
        return $this->iterator[$this->cursor] ?? null;
    }

    /**
     * @return void
     */
    public function next(): void
    {
        ++$this->cursor;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->cursor;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->iterator[$this->cursor]) &&
            \array_key_exists($this->cursor, $this->iterator);
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        $this->cursor = 0;
    }
}
