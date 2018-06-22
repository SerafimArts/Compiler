<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar;

use Railt\Compiler\Grammar\PP2\Parser;
use Railt\Compiler\Reader\GrammarInterface;
use Railt\Compiler\Reader\Result;
use Railt\Io\Readable;
use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class Grammar
 */
class PP2 implements GrammarInterface
{
    /**
     * @var PP2
     */
    private $parser;

    /**
     * @var array
     */
    private $ast = [];

    /**
     * PP2 constructor.
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * @param Readable $grammar
     * @return GrammarInterface
     * @throws \LogicException
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Parser\Exception\UnrecognizedRuleException
     */
    public function add(Readable $grammar): GrammarInterface
    {
        /** @var RuleInterface|LeafInterface $rule */
        foreach ($this->parse($grammar) as $rule) {
            switch ($rule->getName()) {
                case 'Pragma':
                    echo \get_class($rule) . "\n";
                    break;
                case 'Include':
                    echo \get_class($rule) . "\n";
                    break;
                case 'Rule':
                    echo \get_class($rule) . "\n";
                    break;
                case 'Token':
                    echo \get_class($rule) . "\n";
                    break;
            }
        }

        return $this;
    }

    /**
     * @param Readable $grammar
     * @return RuleInterface
     * @throws \LogicException
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Parser\Exception\UnrecognizedRuleException
     */
    private function parse(Readable $grammar): RuleInterface
    {
        return $this->parser->parse($grammar);
    }

    /**
     * @return Result
     */
    public function make(): Result
    {
        foreach ($this->ast as $rule) {
            echo $rule . "\n\n";
        }
        die;
    }
}
