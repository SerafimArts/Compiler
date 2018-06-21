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
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Repetition;
use Railt\Parser\Rule\Symbol;

/**
 * Class RepetitionBuilder
 */
class RepetitionBuilder extends Builder
{
    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * RepetitionBuilder constructor.
     * @param Mapping $mapping
     * @param int $min
     * @param int $max
     * @param null|string $name
     */
    public function __construct(Mapping $mapping, int $min, int $max, ?string $name = null)
    {
        parent::__construct($mapping, $name);
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return Symbol|Production|Concatenation
     */
    public function reduce(): Symbol
    {
        return new Repetition($this->getId(), $this->min, $this->max, $this->children, $this->name);
    }
}
