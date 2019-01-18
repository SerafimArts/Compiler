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

/**
 * Class IncludeDelegate
 */
class IncludeDelegate extends Rule
{
    /**
     * @param Readable $from
     * @return Readable
     * @throws IncludeNotFoundException
     * @throws \Railt\Io\Exception\NotReadableException
     */
    public function getPathname(Readable $from): Readable
    {
        $file = $this->getChild(0)->getValue(1);

        foreach (['', '.pp', '.pp2'] as $ext) {
            $path = \dirname($from->getPathname()) . '/' . $file . $ext;

            if (\is_file($path)) {
                return File::fromPathname($path);
            }
        }

        $exception = new IncludeNotFoundException(\sprintf('Grammar "%s" not found', $file));
        $exception->throwsIn($from, $this->getOffset());

        throw $exception;
    }
}
