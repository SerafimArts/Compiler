<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar;

use Railt\Compiler\Grammar\Delegate\IncludeDelegate;
use Railt\Io\Readable;
use Railt\Lexer\Driver\NativeRegex;
use Railt\Lexer\LexerInterface;
use Railt\Parser\Ast\RuleInterface;
use Railt\Parser\Driver\Llk;
use Railt\Parser\Grammar;
use Railt\Parser\GrammarInterface;
use Railt\Parser\ParserInterface;

/**
 * Class Reader
 */
class Reader
{
    /**
     * @var Readable
     */
    private $file;

    /**
     * @var ParserInterface
     */
    private $pp;

    /**
     * @var LexerInterface
     */
    private $lexer;

    /**
     * @var GrammarInterface
     */
    private $grammar;

    /**
     * Reader constructor.
     * @param Readable $file
     */
    public function __construct(Readable $file)
    {
        $this->file = $file;
        $this->pp = new Parser();
        $this->lexer = new NativeRegex();
        $this->grammar = new Grammar();

        $this->boot();
    }

    /**
     * @return void
     */
    private function boot(): void
    {
        $env = $this->pp->env();

        $env->share(LexerInterface::class, $this->lexer);
        $env->share(GrammarInterface::class, $this->grammar);
        $env->share(self::class, $this);
    }

    /**
     * @return ParserInterface
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Io\Exception\NotReadableException
     */
    public function getParser(): ParserInterface
    {
        $this->addGrammar($this->file);

        return new Llk($this->lexer, $this->grammar);
    }

    /**
     * @param Readable $file
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Io\Exception\NotReadableException
     */
    private function addGrammar(Readable $file): void
    {
        $ast = $this->pp->parse($file);

        foreach ($ast->getChildren() as $child) {
            switch (true) {
                case $child instanceof IncludeDelegate:
                    $this->addGrammar($child->getPathname($file));
                    break;
            }
        }
    }
}
