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
     * @var array|array[]
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

    /**
     * @var array|Readable[]
     */
    private $files = [];

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        if ($this->next($readable, $token)) {
            return;
        }

        if (! \array_key_exists($this->current, $this->rules)) {
            $this->rules[$this->current] = [];
        }

        if (! \array_key_exists($this->current, $this->files)) {
            $this->files[$this->current] = $readable;
        }

        $this->rules[$this->current][] = $token;
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
            $this->resolveCurrent($readable, $token);
            return true;
        }

        if ($this->current === null) {
            $error = \sprintf('Unprocessed production %s', $token->value(0));
            throw (new GrammarException($error))->throwsIn($readable, $token->offset());
        }

        return false;
    }

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function resolveCurrent(Readable $readable, TokenInterface $token): void
    {
        [$name, $delegate, $keep] = [
            \trim($token->value(1), '#'),
            $token->value(2),
            $token->value(1){0} === '#'
        ];

        $this->current = $name;

        if ($keep) {
            $this->keep[] = $this->current;
        }

        if ($delegate) {
            if (! \class_exists($delegate)) {
                $error = \sprintf('Could not found class "%s" to delegate rule "%s"', $delegate, $name);
                throw (new GrammarException($error))->throwsIn($readable, $token->offset());
            }

            $this->delegates[$this->current] = $delegate;
        }
    }

    /**
     * @return array|array[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array|string[]
     */
    public function getKeep(): array
    {
        return $this->keep;
    }

    /**
     * @return array|string[]
     */
    public function getDelegates(): array
    {
        return $this->delegates;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
