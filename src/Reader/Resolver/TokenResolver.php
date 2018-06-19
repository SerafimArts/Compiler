<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver;

use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class TokenResolver
 */
class TokenResolver implements ResolverInterface
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
    private $skip = [];

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        [$name, $value, $group] = $this->info($token);

        $this->tokens[$name] = $value;

        if ($group !== null) {
            $this->groups[$name] = (int)$group;
        }

        if ($token->name() === 'T_SKIP') {
            $this->skip[] = $name;
        }
    }

    /**
     * @param TokenInterface $token
     * @return array
     */
    private function info(TokenInterface $token): array
    {
        return [$token->value(1), $token->value(2), $token->value(3)];
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return array
     */
    public function getSkip(): array
    {
        return $this->skip;
    }
}
