<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Compiler\Reader\BaseTokens;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class TokenResolver
 */
class TokenResolver extends BaseTokens implements ResolverInterface
{
    /**
     * @param Readable $readable
     * @param TokenInterface $token
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        [$name, $value, $group] = $this->info($token);

        $this->setToken($name, $value);

        if ($group !== null) {
            $this->setGroup($name, (int)$group);
        }

        if ($token->name() === 'T_SKIP') {
            $this->makeSkipped($name);
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
}
