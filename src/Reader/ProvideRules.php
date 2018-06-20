<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Io\Readable;
use Railt\Parser\Rule\Symbol;

/**
 * Interface ProvideRules
 */
interface ProvideRules
{
    /**
     * @return array|Symbol[]
     */
    public function all(): array;

    /**
     * @return iterable
     */
    public function getDelegates(): iterable;

    /**
     * @param string $rule
     * @return Readable
     */
    public function getFile(string $rule): Readable;

    /**
     * @param string $rule
     * @return bool
     */
    public function has(string $rule): bool;
}
