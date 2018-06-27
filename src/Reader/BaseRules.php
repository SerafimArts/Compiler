<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Reader;

use Railt\Parser\Ast\Delegate;
use Railt\Parser\Rule\Symbol;

/**
 * Class BaseRules
 */
abstract class BaseRules implements ProvideRules
{
    /**
     * @var array|Symbol[]
     */
    private $rules = [];

    /**
     * @var array|string[]|Delegate[]
     */
    private $delegates = [];

    /**
     * @param Symbol $rule
     * @param string|null $delegate
     */
    protected function add(Symbol $rule, string $delegate = null): void
    {
        $this->rules[$rule->getId()] = $rule;

        if ($delegate) {
            $this->delegates[$rule->getId()] = $delegate;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    protected function isInitialized(int $id): bool
    {
        return \array_key_exists($id, $this->rules);
    }

    /**
     * @param int $id
     * @return Symbol
     */
    protected function fetch(int $id): Symbol
    {
        return $this->rules[$id];
    }

    /**
     * @return array|Symbol[]
     */
    public function all(): array
    {
        \ksort($this->rules);

        return \array_values($this->rules);
    }

    /**
     * @return iterable
     */
    public function getDelegates(): iterable
    {
        return $this->delegates;
    }
}
