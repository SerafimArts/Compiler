<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2\Delegate;

use Railt\Compiler\Exception\IncludeNotFoundException;
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\Rule;

/**
 * Class IncludeDelegate
 */
class IncludeDelegate extends Rule
{
    /**
     * @var string[]
     */
    private const FILE_EXTENSIONS = ['', '.pp', '.pp2'];

    /**
     * @param Readable $from
     * @return Readable
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function getFile(Readable $from): Readable
    {
        $path = $this->getIncludePathname($from);

        try {
            foreach (self::FILE_EXTENSIONS as $ext) {
                if (\is_file($path . $ext)) {
                    return File::fromPathname($path . $ext);
                }
            }
        } catch (\Throwable $e) {
            throw (new IncludeNotFoundException($e->getMessage()))
                ->throwsIn($from, $this->getOffset());
        }

        $error = \sprintf('Could not include file "%s" from "%s"', $path, $from->getPathname());
        throw (new IncludeNotFoundException($error))->throwsIn($from, $this->getOffset());
    }

    /**
     * @param Readable $from
     * @return string
     */
    private function getIncludePathname(Readable $from): string
    {
        $path = \dirname($from->getPathname()) . '/' . $this->getIncludeValue();

        return \str_replace(['\\\\', '\\'], '/', $path);
    }

    /**
     * @return string
     */
    private function getIncludeValue(): string
    {
        /** @var LeafInterface $token */
        $token = $this->first('T_INCLUDE');

        return \trim($token->getValue(1), " \t\n\r\0\x0B\"\\/'");
    }
}
