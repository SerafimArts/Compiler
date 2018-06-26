<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Resolvers;

use Railt\Compiler\Grammar\PP2\Delegate\TokenDefinitionDelegate;
use Railt\Compiler\Grammar\PP2\ResolverInterface;
use Railt\Compiler\Reader\BaseTokens;
use Railt\Io\Readable;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class TokensResolver
 */
class TokensResolver extends BaseTokens implements ResolverInterface
{
    /**
     * @param Readable $readable
     * @param RuleInterface|TokenDefinitionDelegate $rule
     */
    public function resolve(Readable $readable, RuleInterface $rule): void
    {
        $this->setToken($rule->getDefinitionName(), $rule->getDefinitionValue());

        if (! $rule->isKept()) {
            $this->makeSkipped($rule->getDefinitionName());
        }
    }
}
