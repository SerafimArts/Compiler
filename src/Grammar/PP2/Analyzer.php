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
use Railt\Compiler\Grammar\PP2\Builder\AlternationBuilder;
use Railt\Compiler\Grammar\PP2\Builder\Builder;
use Railt\Compiler\Grammar\PP2\Builder\ConcatenationBuilder;
use Railt\Compiler\Grammar\PP2\Builder\TokenBuilder;
use Railt\Compiler\Iterator\LookaheadIterator;
use Railt\Compiler\Reader\ProvideRules;
use Railt\Compiler\Reader\ProvideTokens;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;
use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Terminal;
use Railt\Parser\Rule\Token;

/**
 * Class Analyzer
 */
class Analyzer
{
    /**
     * A list of parsed rules
     *
     * @var array|Symbol[]|Terminal[]|Production[]
     */
    private $parsed = [];

    /**
     * @var array|Builder[]
     */
    private $builders = [];

    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * @var array
     */
    private $ruleTokens = [];

    /**
     * @var ProvideTokens
     */
    private $tokens;

    /**
     * @var Readable
     */
    private $file;

    /**
     * @var ProvideRules
     */
    private $rules;

    /**
     * @var Builder
     */
    private $lastRule;

    /**
     * Analyzer constructor.
     * @param ProvideTokens $tokens
     * @param ProvideRules $rules
     */
    public function __construct(ProvideTokens $tokens, ProvideRules $rules)
    {
        $this->mapping = new Mapping();
        $this->tokens  = $tokens;
        $this->rules   = $rules;
    }

    /**
     * @param string $rule
     * @param iterable $tokens
     */
    public function add(string $rule, iterable $tokens): void
    {
        $this->ruleTokens[$rule] = $tokens;
    }

    /**
     * @param string $rule
     * @return bool
     */
    public function isCompleted(string $rule): bool
    {
        return \array_key_exists($rule, $this->parsed);
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function getResult(): array
    {
        $this->parsed = [];
        $this->parse();

        foreach ($this->builders as $builder) {
            $this->parsed[] = $builder->reduce();
        }

        return $this->parsed;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function parse(): void
    {
        foreach ($this->ruleTokens as $name => $tokens) {
            $this->file = $this->rules->getFile($name);

            $iterator = new LookaheadIterator($tokens);
            $rule     = $this->sequence($iterator);

            if ($this->rules->isKeep($name) && ! $rule->hasName()) {
                $rule->rename($name);
            }

            $this->store($rule);
        }
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    private function store(Builder $builder): Builder
    {
        return $this->builders[] = $this->lastRule = $builder;
    }

    /**
     * @param LookaheadIterator $tokens
     * @return Builder
     * @throws \InvalidArgumentException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function choice(LookaheadIterator $tokens): Builder
    {
        $choice = new AlternationBuilder($this->mapping);
        $choice->addChildBuilder($this->lastRule);
        $tokens->next();

        while ($tokens->valid()) {
            $child = $this->terminal($tokens);

            if ($child) {
                $choice->addChildBuilder($this->store($child));
            }

            $continue = $tokens->getNext() && $tokens->getNext()->name() === Lexer::T_OR;

            if (! $continue) {
                break;
            }

            $tokens->next();
            $tokens->next();
        }

        return $choice;
    }

    /**
     * @return Mapping
     */
    public function getMapping(): Mapping
    {
        return $this->mapping;
    }

    /**
     * @param LookaheadIterator $tokens
     * @return Builder
     * @throws \InvalidArgumentException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function sequence(LookaheadIterator $tokens): Builder
    {
        $children = [];

        while ($tokens->valid()) {
            $child = $this->terminal($tokens);

            if ($child) {
                $children[] = $this->store($child);
            }

            $tokens->next();
        }

        if (\count($children) > 1) {
            $sequence = new ConcatenationBuilder($this->mapping);
            $sequence->addChildrenBuilders($children);

            return $sequence;
        }

        return \reset($children);
    }

    private function repeat(LookaheadIterator $tokens): Builder
    {
    }

    /**
     * @param LookaheadIterator $tokens
     * @return Builder
     * @throws \InvalidArgumentException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function group(LookaheadIterator $tokens): Builder
    {
        $children = [];

        $tokens->next();
        while ($tokens->valid() && $tokens->current()->name() !== Lexer::T_GROUP_CLOSE) {
            $children[] = $tokens->current();
            $tokens->next();
        }

        return $this->sequence(new LookaheadIterator($children));
    }

    /**
     * @param LookaheadIterator $tokens
     * @return null|Builder
     * @throws \InvalidArgumentException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function terminal(LookaheadIterator $tokens): ?Builder
    {
        /** @var TokenInterface $current */
        $current = $tokens->current();

        switch ($current->name()) {
            case Lexer::T_OR:
                return $this->choice($tokens);

            case Lexer::T_ZERO_OR_ONE:
            case Lexer::T_ONE_OR_MORE:
            case Lexer::T_ZERO_OR_MORE:
            case Lexer::T_N_TO_M:
            case Lexer::T_ZERO_TO_M:
            case Lexer::T_N_OR_MORE:
            case Lexer::T_EXACTLY_N:
                return $this->repeat($tokens);

            case Lexer::T_KEPT:
                return $this->token($current, true);

            case Lexer::T_SKIPPED:
                return $this->token($current, false);

            case Lexer::T_INVOKE:
                return $this->invoke($current);

            case Lexer::T_GROUP_OPEN:
                return $this->group($tokens);

            case Lexer::T_RENAME:
                $this->lastRule->rename($current->value(1));
                return null;
        }

        throw (new GrammarException(\sprintf('Unrecognized terminal %s', $current)))
            ->throwsIn($this->file, $current->offset());
    }

    /**
     * @param TokenInterface $token
     * @param bool $keep
     * @return TokenBuilder
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function token(TokenInterface $token, bool $keep): TokenBuilder
    {
        $name = $token->value(1);

        if (! $this->tokens->has($name)) {
            $error = \sprintf('Token "%s" is not defined', $name);
            throw (new GrammarException($error))
                ->throwsIn($this->file, $token->offset());
        }

        return new TokenBuilder($this->mapping, $name, $keep);
    }

    /**
     * @param TokenInterface $invocation
     * @return Builder
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function invoke(TokenInterface $invocation): Builder
    {
        $name = $invocation->value(1);

        if (! $this->rules->has($name)) {
            $error = \sprintf('Rule "%s" is not defined', $name);
            throw (new GrammarException($error))
                ->throwsIn($this->file, $invocation->offset());
        }

        // TODO
    }
}
