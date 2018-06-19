<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader\Resolver\PragmaResolver;

/**
 * Class ConfigResolver
 */
final class ConfigResolver
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var array|string[]
     */
    private $allowed;

    /**
     * ConfigResolver constructor.
     * @param string $prefix
     * @param array $allowed
     */
    public function __construct(string $prefix, array $allowed)
    {
        $this->prefix = \trim($prefix, '.') . '.';
        $this->allowed = $allowed;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function match(string $name): bool
    {
        return \strpos($name, $this->prefix) === 0;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function resolve(string $name): ?string
    {
        $name = \substr($name, \strlen($this->prefix));

        return \in_array($name, $this->allowed, true) ? $name : null;
    }
}
