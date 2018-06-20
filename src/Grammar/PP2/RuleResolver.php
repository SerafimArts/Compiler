<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Compiler\Exception\GrammarException;
use Railt\Compiler\Reader\BaseRules;
use Railt\Io\Exception\ExternalFileException;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;
use Railt\Parser\Rule\Symbol;

/**
 * Class RuleResolver
 */
class RuleResolver extends BaseRules implements ResolverInterface
{
    /**
     * @var array
     */
    private $ruleTokens = [];

    /**
     * @var string|null
     */
    private $current;

    /**
     * @var array
     */
    private $keep = [];

    /**
     * @var TokenResolver
     */
    private $tokens;

    /**
     * RuleResolver constructor.
     * @param TokenResolver $tokens
     */
    public function __construct(TokenResolver $tokens)
    {
        $this->tokens = $tokens;
    }

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

        if (! \array_key_exists($this->current, $this->ruleTokens)) {
            $this->ruleTokens[$this->current] = [];
        }

        $this->ruleTokens[$this->current][] = $token;

        $this->addFile($this->current, $readable);
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
        $this->current = \trim($token->value(1), '#');
        $this->ruleTokens[$this->current] = [];

        if ($token->value(1)[0] === '#') {
            $this->keep[] = $this->current;
        }

        if ($token->value(2)) {
            try {
                $this->addDelegate($this->current, $token->value(2));
            } catch (ExternalFileException $e) {
                throw $e->throwsIn($readable, $token->offset());
            }
        }
    }

    /**
     * @return array|Symbol[]
     * @throws \InvalidArgumentException
     */
    private function analyze(): array
    {
        $analyzer = new Analyzer($this->keep, $this->tokens);

        foreach ($this->ruleTokens as $rule => $tokens) {
            $analyzer->add($rule, $tokens);
        }

        $this->ruleTokens = [];

        return $analyzer->getResult();
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    public function all(): array
    {
        foreach ($this->analyze() as $symbol) {
            $this->add($symbol);
        }

        return parent::all();
    }
}
