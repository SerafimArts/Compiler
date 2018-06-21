<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Builder;

use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Symbol;

/**
 * Class ConcatenationBuilder
 */
class ConcatenationBuilder extends Builder
{
    /**
     * @return Symbol|Production|Concatenation
     */
    public function reduce(): Symbol
    {
        return new Concatenation($this->getId(), $this->children, $this->name);
    }
}
