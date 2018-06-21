<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar;

use Railt\Compiler\Exception\IncludeNotFoundException;
use Railt\Compiler\Exception\UnrecognizedTokenException;
use Railt\Compiler\Grammar\PP2\Lexer;
use Railt\Compiler\Grammar\PP2\Parser;
use Railt\Compiler\Reader\GrammarInterface;
use Railt\Compiler\Reader\Result;
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\Lexer\Result\Eoi;
use Railt\Lexer\Result\Unknown;
use Railt\Lexer\TokenInterface;

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
     * Reader constructor.
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
        $this->ast[] = $this->parser->parse($grammar);

        return $this;
    }

    /**
     * @return Result
     */
    public function make(): Result
    {
        foreach ($this->ast as $rule) {
            echo $rule . "\n\n";
        }
    }
}
