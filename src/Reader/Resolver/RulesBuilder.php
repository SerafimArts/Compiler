<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver;

use Railt\Compiler\Iterator\SlicedIterator;
use Railt\Compiler\Lexer;
use Railt\Compiler\Reader\Resolver\Builder\Builder;
use Railt\Compiler\Reader\Resolver\Builder\ConcatenationBuilder;
use Railt\Compiler\Reader\Resolver\Builder\TokenBuilder;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class RulesBuilder
 */
class RulesBuilder
{
    /**
     * @var array
     */
    private $rules;

    /**
     * @var RuleResolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $parsed = [];

    /**
     * @var \SplStack
     */
    private $stack;

    /**
     * @var Readable|null
     */
    private $file;

    /**
     * @var array
     */
    private $mappings = [];

    /**
     * @var int
     */
    private $lastRuleId = 0;

    /**
     * @var string
     */
    private $text = '';

    /**
     * RulesBuilder constructor.
     * @param RuleResolver $resolver
     */
    public function __construct(RuleResolver $resolver)
    {
        $this->resolver = $resolver;
        $this->rules    = $resolver->getRules();
        $this->stack    = new \SplStack();
    }

    /**
     * @return array
     */
    public function build(): array
    {
        foreach ($this->rules as $rule => $tokens) {
            if ($this->isCompleted($rule)) {
                continue;
            }


            $this->file = $this->resolver->getFiles()[$rule];

            $iterator = new SlicedIterator($tokens);

            $this->stack->push($current = $this->root($rule));

            while ($iterator->valid()) {
                $this->text .= $iterator->current()->value() . ' ';

                $this->reduce($iterator->current(), $iterator);
                $iterator->next();
            }

            $this->complete();
        }

        return $this->parsed;
    }

    /**
     * @param string $rule
     * @return bool
     */
    private function isCompleted(string $rule): bool
    {
        return \array_key_exists($rule, $this->parsed);
    }

    /**
     * @param Builder $builder
     * @return RulesBuilder
     */
    private function add(Builder $builder): self
    {
        $this->current()->jump($builder->getId());
        $this->stack->push($builder);

        return $this;
    }

    /**
     * @param string $rule
     * @return ConcatenationBuilder
     */
    private function root(string $rule): ConcatenationBuilder
    {
        return new ConcatenationBuilder($this->getId($rule), $this->isKept($rule) ? $rule : null);
    }

    /**
     * @param string $rule
     * @return int
     */
    private function getId(string $rule = null): int
    {
        if ($rule === null) {
            return $this->lastRuleId++;
        }

        if (\array_key_exists($rule, $this->mappings)) {
            return $this->mappings[$rule];
        }

        return $this->mappings[$rule] = $this->lastRuleId++;
    }

    /**
     * @param string $rule
     * @return bool
     */
    private function isKept(string $rule): bool
    {
        return \in_array($rule, $this->resolver->getKeep(), true);
    }

    /**
     * @param TokenInterface $token
     * @param SlicedIterator $tokens
     * @return int
     */
    private function reduce(TokenInterface $token, SlicedIterator $tokens): int
    {
        switch ($token->name()) {
            case Lexer::T_KEPT:
                return $this->reduceToken($token, true);

            case Lexer::T_SKIPPED:
                return $this->reduceToken($token, false);

            //default:
            //    $error = \sprintf('Unexpected token %s', $token);
            //    throw (new GrammarException($error))->throwsIn($this->file, $token->offset());
        }

        return 0;
    }

    /**
     * @param TokenInterface $token
     * @param bool $keep
     * @return int
     */
    private function reduceToken(TokenInterface $token, bool $keep): int
    {
        $key = $token->value(1) . ':' . $token->name();

        $builder = new TokenBuilder($this->getId($key), $token->value(1), $keep);

        $this->add($builder)->complete();

        return $builder->getId();
    }

    private function reduceConcat(SlicedIterator $iterator): void
    {
        dd($iterator);
    }

    /**
     * @return RulesBuilder
     */
    private function complete(): self
    {
        /** @var Builder $rule */
        $rule     = $this->stack->pop();
        $instance = $rule->build();

        if ($rule->getName()) {
            $this->parsed[$rule->getName()] = $instance;
        } else {
            $this->parsed[] = $instance;
        }

        $instance->{'--grammar--'} = $this->text;
        $this->text                = '';

        return $this;
    }

    /**
     * @return Builder
     */
    private function current(): Builder
    {
        return $this->stack->top();
    }
}
