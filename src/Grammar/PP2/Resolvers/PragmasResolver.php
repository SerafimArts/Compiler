<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Resolvers;

use Railt\Compiler\Exception\UnknownPragmaException;
use Railt\Compiler\Grammar\PP2\Delegate\PragmaDefinitionDelegate;
use Railt\Compiler\Grammar\PP2\ResolverInterface;
use Railt\Compiler\Reader\BasePragmas;
use Railt\Io\Exception\ExternalFileException;
use Railt\Io\Readable;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class PragmasResolver
 */
class PragmasResolver extends BasePragmas implements ResolverInterface
{
    /**
     * @param Readable $readable
     * @param RuleInterface|PragmaDefinitionDelegate $rule
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function resolve(Readable $readable, RuleInterface $rule): void
    {
        [$name, $value] = [$rule->getPragmaName(), $rule->getPragmaValue()];

        foreach ($this->getResolvers() as $group => $resolver) {
            if ($resolver->match($name)) {
                $resolved = $resolver->resolve($name);

                if (! $resolved) {
                    throw $this->badPragma($name, $value)->throwsIn($readable, $rule->getOffset());
                }

                $this->set($group, $name, $value);
            }
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @return ExternalFileException
     */
    private function badPragma(string $name, string $value): ExternalFileException
    {
        $error = \sprintf('Unknown pragma name "%s" with value "%s"', $name, $value);

        return new UnknownPragmaException($error);
    }

    /**
     * @return void
     */
    public function make(): void
    {
        // Do nothing
    }
}
