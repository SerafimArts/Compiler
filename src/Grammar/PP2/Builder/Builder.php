<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Builder;

use Railt\Compiler\Grammar\PP2\Mapping;
use Railt\Parser\Rule\Symbol;

/**
 * Class Builder
 */
abstract class Builder
{
    /**
     * @var array
     */
    protected $children = [];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Mapping
     */
    protected $mapper;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * Builder constructor.
     * @param Mapping $mapper
     * @param string|null $name
     */
    public function __construct(Mapping $mapper, string $name = null)
    {
        $this->mapper = $mapper;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        if ($this->id === null) {
            $this->id = $this->mapper->id($this->name);
        }

        return $this->id;
    }

    /**
     * @param string $name
     */
    public function rename(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $child
     */
    public function addChild(int $child): void
    {
        $this->children[] = $child;
    }

    /**
     * @param Builder $builder
     */
    public function addChildBuilder(Builder $builder): void
    {
        $this->addChild($builder->getId());
    }

    /**
     * @param array $builders
     */
    public function addChildrenBuilders(array $builders): void
    {
        foreach ($builders as $builder) {
            $this->addChildBuilder($builder);
        }
    }

    /**
     * @return bool
     */
    public function hasName(): bool
    {
        return $this->name !== null;
    }

    /**
     * @return Symbol
     */
    abstract public function reduce(): Symbol;
}
