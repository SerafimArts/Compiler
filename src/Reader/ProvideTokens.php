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
 * Interface ProvideTokens
 */
interface ProvideTokens
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $token
     * @return bool
     */
    public function isKeep(string $token): bool;

    /**
     * @param string $token
     * @return int
     */
    public function getGroup(string $token): int;

    /**
     * @param string $token
     * @return bool
     */
    public function has(string $token): bool;
}
