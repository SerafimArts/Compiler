<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Io\Readable;
use Railt\Parser\Ast\RuleInterface;

/**
 * Interface ResolverInterface
 */
interface ResolverInterface
{
    /**
     * @param Readable $readable
     * @param RuleInterface $ast
     */
    public function resolve(Readable $readable, RuleInterface $ast): void;

    /**
     * @return void
     */
    public function make(): void;
}
