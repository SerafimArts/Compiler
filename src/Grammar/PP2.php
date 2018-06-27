<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar;

use Railt\Compiler\Grammar\PP2\Delegate\IncludeDelegate;
use Railt\Compiler\Grammar\PP2\Delegate\PragmaDefinitionDelegate;
use Railt\Compiler\Grammar\PP2\Delegate\RuleDefinitionDelegate;
use Railt\Compiler\Grammar\PP2\Delegate\TokenDefinitionDelegate;
use Railt\Compiler\Grammar\PP2\Parser;
use Railt\Compiler\Grammar\PP2\Resolvers\PragmasResolver;
use Railt\Compiler\Grammar\PP2\Resolvers\RulesResolver;
use Railt\Compiler\Grammar\PP2\Resolvers\TokensResolver;
use Railt\Compiler\Reader\GrammarInterface;
use Railt\Compiler\Reader\ProvidePragmas;
use Railt\Compiler\Reader\ProvideRules;
use Railt\Compiler\Reader\ProvideTokens;
use Railt\Compiler\Reader\Result;
use Railt\Io\Readable;
use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\RuleInterface;
use Railt\Parser\Environment;
use Railt\Parser\Exception\UnrecognizedRuleException;

/**
 * Class Grammar
 */
class PP2 implements GrammarInterface
{
    public const ENV_RULES   = 'rules';
    public const ENV_TOKENS  = 'tokens';
    public const ENV_PRAGMAS = 'pragmas';

    /**
     * @var array|string[]
     */
    private $loaded = [];

    /**
     * @var PP2
     */
    private $parser;

    /**
     * @var Environment
     */
    private $env;

    /**
     * @var ProvidePragmas|PragmasResolver
     */
    private $pragmas;

    /**
     * @var ProvideRules|RulesResolver
     */
    private $rules;

    /**
     * @var ProvideTokens|TokensResolver
     */
    private $tokens;

    /**
     * PP2 constructor.
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->parser = new Parser();

        $this->bootResolvers();
        $this->bootEnvironment();
    }

    /**
     * @return void
     */
    private function bootResolvers(): void
    {
        $this->pragmas = new PragmasResolver();
        $this->rules   = new RulesResolver();
        $this->tokens  = new TokensResolver();
    }

    /**
     * @return void
     */
    private function bootEnvironment(): void
    {
        $this->env = $this->createEnvironment();
    }

    /**
     * @return Environment
     */
    private function createEnvironment(): Environment
    {
        $env = new Environment();
        $env->share(static::ENV_RULES, $this->rules);
        $env->share(static::ENV_TOKENS, $this->tokens);
        $env->share(static::ENV_PRAGMAS, $this->pragmas);

        return $env;
    }

    /**
     * @return Result
     */
    public function make(): Result
    {
        $this->pragmas->make();
        $this->tokens->make();
        $this->rules->make();

        return new Result($this->pragmas, $this->tokens, $this->rules);
    }

    /**
     * @param Readable $grammar
     * @throws UnrecognizedRuleException
     * @throws \LogicException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function load(Readable $grammar): void
    {
        $ast = $this->parse($grammar);

        dd((string)$ast);

        /** @var RuleInterface|LeafInterface $rule */
        foreach ($ast as $rule) {
            switch ($rule->getName()) {
                case 'Pragma':
                    /** @var PragmaDefinitionDelegate $rule */
                    $this->pragmas->resolve($grammar, $rule);
                    break;

                case 'Include':
                    /** @var IncludeDelegate $rule */
                    $this->add($rule->getFile($grammar));
                    break;

                case 'Rule':
                    /** @var RuleDefinitionDelegate $rule */
                    $this->rules->resolve($grammar, $rule);
                    break;

                case 'Token':
                    /** @var TokenDefinitionDelegate $rule */
                    $this->tokens->resolve($grammar, $rule);
                    break;

                default:
                    $error = \sprintf('Unprocessable rule %s', $rule->getName());
                    throw (new UnrecognizedRuleException($error))->throwsIn($grammar, $rule->getOffset());
            }
        }
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
        return $this->parser->parse($grammar, $this->env);
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
        if (! $this->isLoaded($grammar)) {
            $this->load($grammar);
        }

        return $this;
    }

    /**
     * @param Readable $grammar
     * @return bool
     */
    private function isLoaded(Readable $grammar): bool
    {
        $loaded = \in_array($grammar->getHash(), $this->loaded, true);

        if (! $loaded) {
            $this->loaded[] = $grammar->getHash();
        }

        return $loaded;
    }
}
