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
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Production;

/**
 * Class Group
 */
class Group
{
    /**
     * @var LookaheadIterator
     */
    private $iterator;

    /**
     * @var Analyzer
     */
    private $analyzer;

    /**
     * @var string
     */
    private $rule;

    /**
     * Group constructor.
     * @param LookaheadIterator $iterator
     * @param Analyzer $analyzer
     * @param string|null $rule
     */
    public function __construct(LookaheadIterator $iterator, Analyzer $analyzer, string $rule = null)
    {
        $this->iterator = $iterator;
        $this->analyzer = $analyzer;
        $this->rule     = $rule;
    }

    /**
     * @param string|null $rule
     * @return int
     */
    private function id(string $rule = null): int
    {
        return $this->analyzer->getMapping()->id($rule);
    }

    /**
     * @return Production
     */
    public function reduce(): Production
    {
        return new Concatenation($this->id($this->rule), [], $this->rule);
    }
}
