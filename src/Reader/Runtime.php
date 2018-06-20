<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Compiler\Reader\Resolver\PragmaResolver;
use Railt\Compiler\Reader\Resolver\RuleResolver;
use Railt\Compiler\Reader\Resolver\TokenResolver;
use Railt\Lexer\Driver\NativeStateless;
use Railt\Lexer\LexerInterface;
use Railt\Parser\Parser;
use Railt\Parser\ParserInterface;

/**
 * Class Runtime
 */
class Runtime
{
    /**
     * @var ProvideRules
     */
    private $rules;

    /**
     * @var ProvidePragmas
     */
    private $config;

    /**
     * @var ProvideTokens
     */
    private $tokens;

    /**
     * Runtime constructor.
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->rules  = $result->getRules();
        $this->tokens = $result->getTokens();
        $this->config = $result->getPragmas();
    }

    /**
     * @return ParserInterface
     * @throws \InvalidArgumentException
     */
    public function getParser(): ParserInterface
    {
        $parser = new Parser($this->getLexer(), $this->rules->all(), $this->config->parser());

        foreach ($this->rules->getDelegates() as $name => $delegate) {
            $parser->addDelegate($name, $delegate);
        }

        return $parser;
    }

    /**
     * @return LexerInterface
     */
    public function getLexer(): LexerInterface
    {
        $lexer = new NativeStateless();

        foreach ($this->tokens->all() as $name => $pcre) {
            $lexer->add($name, $pcre, ! $this->tokens->isKeep($name));
        }

        return $lexer;
    }
}
