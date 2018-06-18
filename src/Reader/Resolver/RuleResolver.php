<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver;

use Railt\Compiler\Exception\GrammarException;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class RuleResolver
 */
class RuleResolver implements ResolverInterface
{
    /**
     * @var array|string[]
     */
    private $rules = [];

    /**
     * @var string|null
     */
    private $current;

    /**
     * @var array|string[]
     */
    private $keep = [];

    /**
     * @var array|string[]
     */
    private $delegates = [];

    public function resolve(Readable $readable, TokenInterface $token): void
    {
        if ($this->next($readable, $token)) {
            return;
        }
    }

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @return bool
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function next(Readable $readable, TokenInterface $token): bool
    {
        if ($token->name() === 'T_NODE_DEFINITION') {
            $this->current = $token->value(1);
            return true;
        }

        if ($this->current === null) {
            $error = \sprintf('Unprocessed production %s', $token->value(0));
            throw (new GrammarException($error))->throwsIn($readable, $token->offset());
        }

        return false;
    }
}
