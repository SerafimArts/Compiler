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
use Railt\Parser\Rule\Token;

/**
 * Class TokenBuilder
 */
class TokenBuilder extends Builder
{
    /**
     * @var bool
     */
    private $keep;

    /**
     * TokenBuilder constructor.
     * @param Mapping $mapper
     * @param string $name
     * @param bool $keep
     */
    public function __construct(Mapping $mapper, string $name, bool $keep)
    {
        $this->keep = $keep;
        parent::__construct($mapper, $name);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        if ($this->id === null) {
            $name = $this->name . '<' . ($this->keep ? '+' : '-') . '>';

            $this->id = $this->mapper->id($name);
        }

        return $this->id;
    }

    /**
     * @return Symbol
     */
    public function reduce(): Symbol
    {
        return new Token($this->getId(), $this->name, $this->keep);
    }
}
