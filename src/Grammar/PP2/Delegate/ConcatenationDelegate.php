<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Parser\Ast\Rule;
use Railt\Parser\Rule\Symbol;

/**
 * Class ConcatenationDelegate
 */
class ConcatenationDelegate extends Rule implements Reduced
{
    public function reduce(): Symbol
    {
        throw new \LogicException('The ' . __METHOD__ . ' not implemented yet');
    }
}
