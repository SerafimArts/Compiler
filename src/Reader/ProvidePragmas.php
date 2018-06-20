<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

/**
 * Interface ProvidePragmas
 */
interface ProvidePragmas
{
    /**
     * @return array
     */
    public function parser(): array;

    /**
     * @return array
     */
    public function lexer(): array;

    /**
     * @return array
     */
    public function grammar(): array;
}
