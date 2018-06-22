<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Parser\Rule\Symbol;

/**
 * Interface Reduced
 */
interface Reduced
{
    /**
     * @return Symbol
     */
    public function reduce(): Symbol;
}
