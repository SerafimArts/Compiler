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

/**
 * Interface GrammarInterface
 */
interface GrammarInterface
{
    /**
     * @param Readable $grammar
     * @return GrammarInterface
     */
    public function add(Readable $grammar): self;

    /**
     * @return Result
     */
    public function make(): Result;
}
