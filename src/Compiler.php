<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler;

use Railt\Compiler\Grammar\Reader;
use Railt\Io\Readable;
use Railt\Parser\Driver\Proxy;
use Railt\Parser\ParserInterface;
use Zend\Code\Generator\ValueGenerator as Value;

/**
 * Class Compiler
 */
class Compiler extends Proxy
{
    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var string
     */
    private $class = 'Parser';

    /**
     * @param Readable $grammar
     * @return Compiler
     */
    public static function read(Readable $grammar): Compiler
    {
        $reader = new Reader($grammar);

        return new static($reader->getParser());
    }

    /**
     * @param ParserInterface $parser
     * @return Compiler
     */
    public static function fromParser(ParserInterface $parser): Compiler
    {
        return new static($parser);
    }

    /**
     * @param string $namespace
     * @return Compiler
     */
    public function setNamespace(string $namespace): Compiler
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param string $name
     * @return Compiler
     */
    public function setClassName(string $name): Compiler
    {
        $this->class = $name;

        return $this;
    }

    /**
     * @param string $path
     */
    public function saveTo(string $path): void
    {
        $pathName = $path . '/' . $this->class . '.php';

        \file_put_contents($pathName, $this->build());
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function build(): string
    {
        \ob_start();

        try {
            require __DIR__ . '/../resources/parser.tpl.php';
            return \ob_get_contents();
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            \ob_end_clean();
        }
    }

    /**
     * @param mixed $value
     * @return string
     * @throws \Zend\Code\Exception\InvalidArgumentException
     * @throws \Zend\Code\Generator\Exception\RuntimeException
     */
    protected function render($value): string
    {
        $generator = new Value($value, Value::TYPE_AUTO, Value::OUTPUT_SINGLE_LINE);

        return $generator->generate();
    }
}
