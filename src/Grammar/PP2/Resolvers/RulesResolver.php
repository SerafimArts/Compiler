<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Resolvers;

use Railt\Compiler\Grammar\PP2\Delegate\Production\BaseProductionDelegate;
use Railt\Compiler\Grammar\PP2\Delegate\RuleDefinitionDelegate;
use Railt\Compiler\Grammar\PP2\ResolverInterface;
use Railt\Compiler\Reader\BaseRules;
use Railt\Io\Readable;
use Railt\Parser\Ast\RuleInterface;
use Railt\Parser\Rule\Symbol;

/**
 * Class PragmasResolver
 */
class RulesResolver extends BaseRules implements ResolverInterface
{
    /**
     * @var array|RuleDefinitionDelegate[]
     */
    private $store = [];

    /**
     * @param Readable $readable
     * @param RuleInterface|RuleDefinitionDelegate $ast
     */
    public function resolve(Readable $readable, RuleInterface $ast): void
    {
        $ast->getProduction()->setFile($readable);

        $this->store[$ast->getRuleName()] = $ast;
    }

    /**
     * @param string $rule
     * @return bool
     */
    public function hasRule(string $rule): bool
    {
        return \array_key_exists($rule, $this->store);
    }

    /**
     * @param string $rule
     * @return Symbol
     */
    public function getRule(string $rule): Symbol
    {
        $production = $this->store[$rule]->getProduction();

        if (! $this->isInitialized($production->getId())) {
            $this->create($this->store[$rule]);
        }

        return $production->getRuleDefinition();
    }

    /**
     * @return void
     */
    public function make(): void
    {
        foreach ($this->store as $name => $rule) {
            if (! $this->isInitialized($rule->getId())) {
                $this->create($rule);
            }
        }
    }

    /**
     * @param RuleDefinitionDelegate $delegate
     */
    private function create(RuleDefinitionDelegate $delegate): void
    {
        $this->share($delegate->getProduction());
    }

    /**
     * @param BaseProductionDelegate $rule
     */
    protected function share(BaseProductionDelegate $rule): void
    {
        $this->add($rule->getRuleDefinition());

        foreach ($rule->getChildren() as $child) {
            if ($child instanceof BaseProductionDelegate) {
                $this->share($child);
            }
        }
    }
}
