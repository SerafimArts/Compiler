<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver\Builder;

use Railt\Parser\Rule\Symbol;

/**
 * Class Builder
 */
abstract class Builder
{
    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array|int[]
     */
    protected $children = [];

    /**
     * Builder constructor.
     * @param int $id
     * @param null|string $name
     */
    public function __construct(int $id, string $name = null)
    {
        $this->name = $name;
        $this->id   = $id;
    }

    /**
     * @param int $rule
     * @return Builder
     */
    public function jump(int $rule): self
    {
        $this->children[] = $rule;

        return $this;
    }

    /**
     * @param null|string $name
     * @return Builder
     */
    public function rename(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Symbol
     */
    abstract public function build(): Symbol;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \class_basename($this);
    }
}
