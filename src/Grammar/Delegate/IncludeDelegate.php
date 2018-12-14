<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\Delegate;

use Railt\Compiler\Exception\IncludeNotFoundException;
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Ast\RuleInterface;

/**
 * Class IncludeDelegate
 */
class IncludeDelegate extends Rule
{
    /**
     * IncludeDelegate constructor.
     * @param RuleInterface $rule
     */
    public function __construct(RuleInterface $rule)
    {
        parent::__construct($rule->getName(), $rule->getChildren(), $rule->getOffset());
    }

    /**
     * @param Readable $from
     * @return Readable
     * @throws \Railt\Io\Exception\NotReadableException
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function getPathname(Readable $from): Readable
    {
        $file = $this->getValue(1);

        foreach (['', '.pp', '.pp2'] as $ext) {
            $path = \dirname($from->getPathname()) . '/' . $file . $ext;

            if (\is_file($path)) {
                return File::fromPathname($path);
            }
        }

        throw (new IncludeNotFoundException(\sprintf('Grammar "%s" not found', $file)))
            ->throwsIn($from, $this->getOffset());
    }
}
