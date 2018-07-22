<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Console;

use Railt\Compiler\Compiler;
use Railt\Io\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AnalyzeCommand
 */
class AnalyzeCommand extends Command
{
    /**
     * @param InputInterface $in
     * @param OutputInterface $out
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Io\Exception\NotReadableException
     * @throws \Throwable
     */
    public function execute(InputInterface $in, OutputInterface $out): void
    {
        $compiler = Compiler::load(File::fromPathname($in->getArgument('grammar')));

        $ast = $compiler->parse(File::fromPathname($in->getArgument('source')));

        $ast = $in->getOption('root') ? $ast->first($in->getOption('root')) : $ast;

        $out->write((string)$ast);
    }

    /**
     * @return void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function configure(): void
    {
        $this->setName('compiler:analyze');
        $this->setDescription('Analyze source file using selection grammar');

        $this->addArgument('grammar', InputArgument::REQUIRED,
            'Input pp2 grammar file');

        $this->addArgument('source', InputArgument::REQUIRED,
            'Input source file for parsing');

        $this->addOption('root', 'r', InputOption::VALUE_OPTIONAL,
            'Sets an AST root node for dumping');
    }
}
