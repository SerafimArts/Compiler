<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver;

use Railt\Compiler\Exception\UnknownPragmaException;
use Railt\Compiler\Reader\Resolver\PragmaResolver\ConfigResolver;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;
use Railt\Parser\Configuration;

/**
 * Class PragmaResolver
 */
class PragmaResolver implements ResolverInterface
{
    /**
     * @var string
     */
    private const PARSER_RESOLVER = 'parser';

    /**
     * @var array|ConfigResolver[]
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
        $this->resolvers = [
            self::PARSER_RESOLVER => new ConfigResolver(self::PARSER_RESOLVER, [
                Configuration::PRAGMA_ROOT,
                Configuration::PRAGMA_LOOKAHEAD,
                Configuration::PRAGMA_RUNTIME,
            ]),
        ];
    }

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        [$name, $value] = [$token->value(1), $token->value(2)];

        foreach ($this->resolvers as $group => $resolver) {
            if ($resolver->match($name)) {
                $name = $this->resolvePragmaName($readable, $token, $resolver->resolve($name));
                $this->set($group, $name, $value);
                return;
            }
        }
    }

    /**
     * @param string $group
     * @param string $name
     * @param string $value
     */
    private function set(string $group, string $name, string $value): void
    {
        if (! \array_key_exists($group, $this->configs)) {
            $this->configs[$group] = [];
        }

        $this->configs[$group][$name] = $value;
    }

    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @param null|string $name
     * @return string
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    private function resolvePragmaName(Readable $readable, TokenInterface $token, ?string $name): string
    {
        if ($name === null) {
            $error = \vsprintf('Unknown configuration pragma rule "%s" with value "%s"', [
                $token->value(1),
                $token->value(2),
            ]);

            throw (new UnknownPragmaException($error))->throwsIn($readable, $token->offset());
        }

        return $name;
    }

    /**
     * @param string $group
     * @return iterable
     */
    public function all(string $group): iterable
    {
        return $this->configs[$group] ?? [];
    }
}
