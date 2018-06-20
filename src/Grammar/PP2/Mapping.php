<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

/**
 * Class Mapping
 */
class Mapping
{
    /**
     * @var array
     */
    private $mappings = [];

    /**
     * @var int
     */
    private $lastId = 0;

    /**
     * @param string|null $rule
     * @return int
     */
    public function id(string $rule = null): int
    {
        if ($rule === null) {
            return $this->lastId++;
        }

        if (! $this->has($rule)) {
            $this->mappings[$rule] = $this->lastId++;
        }

        return $this->mappings[$rule];
    }

    /**
     * @param string $rule
     * @return bool
     */
    public function has(string $rule): bool
    {
        return \array_key_exists($rule, $this->mappings);
    }
}
