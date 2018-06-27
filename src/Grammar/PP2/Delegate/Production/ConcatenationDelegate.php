<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate\Production;

use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Symbol;

/**
 * Class ConcatenationDelegate
 */
class ConcatenationDelegate extends BaseProductionDelegate
{
    /**
     * @return Symbol
     */
    public function create(): Symbol
    {
        return new Concatenation($this->getId(), $this->getChildrenIds(), $this->symbolName);
    }
}
