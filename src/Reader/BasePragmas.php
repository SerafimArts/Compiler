<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Parser\Configuration;

/**
 * Class BasePragmas
 */
abstract class BasePragmas implements ProvidePragmas
{
    /**
     * @var string
     */
    public const GROUP_PARSER = 'parser';

    /**
     * @var string
     */
    public const GROUP_LEXER = 'lexer';

    /**
     * @var string
     */
    public const GROUP_GRAMMAR = 'grammar';

    /**
     * @var array|Config[]
     */
    private $resolvers;

    /**
     * @var array
     */
    private $configs = [];

    /**
     * PragmaResolver constructor.
     */
    public function __construct()
    {
        $this->bootResolvers();
    }

    /**
     * @return void
     */
    private function bootResolvers(): void
    {
        $this->resolvers[self::GROUP_PARSER]  = $this->getParserResolver();
        $this->resolvers[self::GROUP_LEXER]   = $this->getLexerResolver();
        $this->resolvers[self::GROUP_GRAMMAR] = $this->getGrammarResolver();
    }

    /**
     * @return Config
     */
    private function getParserResolver(): Config
    {
        return new Config(self::GROUP_PARSER, [
            Configuration::PRAGMA_ROOT,
            Configuration::PRAGMA_LOOKAHEAD,
            Configuration::PRAGMA_RUNTIME,
        ]);
    }

    /**
     * @return Config
     */
    private function getLexerResolver(): Config
    {
        return new Config(self::GROUP_LEXER, [
            // TODO
        ]);
    }

    /**
     * @return Config
     */
    private function getGrammarResolver(): Config
    {
        return new Config(self::GROUP_GRAMMAR, [
            // TODO
        ]);
    }

    /**
     * @return array
     */
    public function parser(): array
    {
        return $this->configs[self::GROUP_GRAMMAR] ?? [];
    }

    /**
     * @return array
     */
    public function lexer(): array
    {
        return $this->configs[self::GROUP_LEXER] ?? [];
    }

    /**
     * @return array
     */
    public function grammar(): array
    {
        return $this->configs[self::GROUP_GRAMMAR] ?? [];
    }

    /**
     * @return iterable|Config[]
     */
    protected function getResolvers(): iterable
    {
        return $this->resolvers;
    }

    /**
     * @param string $group
     * @param string $name
     * @param string $value
     */
    protected function set(string $group, string $name, string $value): void
    {
        if (! \array_key_exists($group, $this->configs)) {
            $this->configs[$group] = [];
        }

        $this->configs[$group][$name] = $value;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->configs;
    }
}
