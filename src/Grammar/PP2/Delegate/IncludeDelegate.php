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
use Railt\Compiler\Grammar\PP2;
use Railt\Io\File;
use Railt\Io\Readable;
use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\Rule;
use Railt\Parser\Environment;

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
     * @var Readable
     */
    private $file;

    /**
     * IncludeDelegate constructor.
     * @param Environment $env
     * @param string $name
     * @param array $children
     * @param int $offset
     */
    public function __construct(Environment $env, string $name, array $children = [], int $offset = 0)
    {
        $this->file = $env->get(PP2::ENV_FILE);
        parent::__construct($env, $name, $children, $offset);
    }

    /**
     * @return Readable
     * @throws \Railt\Io\Exception\ExternalFileException
     */
    public function getFile(): Readable
    {
        $path = $this->getIncludePathname();

        try {
            foreach (self::FILE_EXTENSIONS as $ext) {
                if (\is_file($path . $ext)) {
                    return File::fromPathname($path . $ext);
                }
            }

        } catch (\Throwable $e) {
            throw (new IncludeNotFoundException($e->getMessage()))
                ->throwsIn($this->file, $this->getOffset());
        }

        $error = \sprintf('Could not include file "%s" from "%s"', $path, $this->file->getPathname());
        throw (new IncludeNotFoundException($error))->throwsIn($this->file, $this->getOffset());
    }

    /**
     * @return string
     */
    private function getIncludePathname(): string
    {
        $path = \dirname($this->file->getPathname()) . '/' . $this->getIncludeValue();

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
