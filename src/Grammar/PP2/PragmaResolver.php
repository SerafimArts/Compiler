<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Compiler\Reader\BasePragmas;
use Railt\Io\Readable;
use Railt\Lexer\TokenInterface;

/**
 * Class PragmaResolver
 */
class PragmaResolver extends BasePragmas implements ResolverInterface
{
    /**
     * @param Readable $readable
     * @param TokenInterface $token
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function resolve(Readable $readable, TokenInterface $token): void
    {
        [$name, $value] = [$token->value(1), $token->value(2)];

        foreach ($this->getResolvers() as $group => $resolver) {
            if ($resolver->match($name)) {
                $name = $this->verifyPragmaName($readable, $token, $resolver->resolve($name));

                $this->set($group, $name, $value);
            }
        }
    }
}
