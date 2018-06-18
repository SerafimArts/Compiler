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
     * @param Readable $readable
     * @param TokenInterface $token
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        $this->tokens[$token->value(1)] = $token->value(2);

        if ($token->value(3)) {
            $this->groups[$token->value(1)] = $token->value(3);
        }
    }
}
