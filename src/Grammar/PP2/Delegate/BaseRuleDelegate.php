<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Compiler\Grammar\PP2;
use Railt\Compiler\Grammar\PP2\Mapping;
use Railt\Parser\Ast\Delegate;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Rule\Symbol;

/**
 * Class BaseRuleDelegate
 */
abstract class BaseRuleDelegate extends Rule implements ProvidesSymbol
{
    /**
     * @return Mapping
     */
    protected function map(): Mapping
    {
        return $this->env(PP2::ENV_MAP);
    }

    /**
     * @param string|null $rule
     * @return int
     */
    protected function getId(string $rule = null): int
    {
        return $this->map()->id($rule);
    }

    /**
     * @return array|int[]
     */
    protected function getChildrenRuleIds(): array
    {
        $result = [];

        if ($this instanceof ProvidesChildrenSymbol) {
            foreach ($this->getChildrenRules() as $rule) {
                $result[] = $rule->getId();
            }
        }

        return $result;
    }

    /**
     * @return iterable|Symbol[]
     */
    public function getChildrenRules(): iterable
    {
        /** @var ProvidesSymbol $child */
        foreach ($this->getChildren() as $child) {
            yield $child->getRule();

            if ($child instanceof ProvidesChildrenSymbol) {
                yield from $child->getChildrenRules();
            }
        }
    }
}
