<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Compiler\Iterator\LookaheadIterator;
use Railt\Compiler\Reader\ProvideTokens;
use Railt\Parser\Rule\Production;
use Railt\Parser\Rule\Symbol;
use Railt\Parser\Rule\Terminal;

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
     * A list of kept rule names.
     *
     * @var array
     */
    private $keep;

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
     * Analyzer constructor.
     * @param array $keep
     * @param ProvideTokens $tokens
     */
    public function __construct(array $keep, ProvideTokens $tokens)
    {
        $this->keep = $keep;
        $this->mapping = new Mapping();
        $this->tokens = $tokens;
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
     */
    public function getResult(): array
    {
        $this->parsed = [];
        $this->parse();

        return $this->parsed;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function parse(): void
    {
        foreach ($this->ruleTokens as $rule => $tokens) {
            $this->store($this->build($rule, $tokens));
        }
    }

    /**
     * @param Symbol $symbol
     */
    public function store(Symbol $symbol): void
    {
        $this->parsed[] = $symbol;
    }

    /**
     * @return Mapping
     */
    public function getMapping(): Mapping
    {
        return $this->mapping;
    }

    /**
     * @param string $rule
     * @param array $tokens
     * @return Production
     * @throws \InvalidArgumentException
     */
    public function build(string $rule, array $tokens): Production
    {
        return $this->group(new LookaheadIterator($tokens), $rule)->reduce();
    }

    /**
     * @param string $rule
     * @return int
     * @throws \InvalidArgumentException
     */
    public function fetchId(string $rule): int
    {
        if ($this->mapping->has($rule)) {
            return $this->mapping->id($rule);
        }

        return $this->build($rule, $this->ruleTokens[$rule])->getId();
    }

    /**
     * @param LookaheadIterator $iterator
     * @param string|null $rule
     * @return Group
     */
    public function group(LookaheadIterator $iterator, string $rule = null): Group
    {
        if ($rule && ! \in_array($rule, $this->keep, true)) {
            $rule = null;
        }

        return new Group($iterator, $this, $rule);
    }
}
