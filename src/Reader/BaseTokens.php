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
 * Class BaseTokens
 */
abstract class BaseTokens implements ProvideTokens
{
    /**
     * @var array
     */
    private $tokens = [];

    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var array
     */
    private $skipped = [];

    /**
     * @param string $token
     * @param string $pcre
     */
    protected function setToken(string $token, string $pcre): void
    {
        $this->tokens[$token] = $pcre;
    }

    /**
     * @param string $token
     */
    protected function makeSkipped(string $token): void
    {
        $this->skipped[] = $token;
    }

    /**
     * @param string $token
     * @param int $group
     */
    protected function setGroup(string $token, int $group): void
    {
        $this->groups[$token] = $group;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->tokens;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isKeep(string $token): bool
    {
        return ! \in_array($token, $this->skipped, true);
    }

    /**
     * @param string $token
     * @return int
     */
    public function getGroup(string $token): int
    {
        return $this->groups[$token] ?? 0;
    }
}
