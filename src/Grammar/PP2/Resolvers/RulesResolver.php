<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Resolvers;

use Railt\Compiler\Grammar\PP2\Delegate\RuleDelegate;
use Railt\Compiler\Grammar\PP2\Mapping;
use Railt\Compiler\Grammar\PP2\ResolverInterface;
use Railt\Compiler\Reader\BaseRules;
use Railt\Io\Exception\ExternalFileException;
use Railt\Io\Readable;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class PragmasResolver
 */
class RulesResolver extends BaseRules implements ResolverInterface
{
    /**
     * @var Mapping
     */
    private $map;

    /**
     * RulesResolver constructor.
     * @param Mapping $map
     */
    public function __construct(Mapping $map)
    {
        $this->map = $map;
    }

    /**
     * @param Readable $readable
     * @param RuleInterface|RuleDelegate $rule
     * @throws \LogicException
     */
    public function resolve(Readable $readable, RuleInterface $rule): void
    {
        if ($this->has($rule->getRuleName())) {
            return;
        }
    }

    /**
     * @throws ExternalFileException
     */
    protected function build(): void
    {
        $current = $rule->getRule();
        $this->add($current);

        foreach ($rule->getChildrenRules() as $child) {
            $this->add($child);
        }

        try {
            if ($delegate = $rule->getDelegate()) {
                // Delegate: $rule->getRuleName() => $delegate
            }
        } catch (ExternalFileException $e) {
            throw $e->throwsIn($readable, $rule->getOffset());
        }
    }
}

