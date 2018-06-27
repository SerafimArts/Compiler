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
use Railt\Parser\Configuration;
use Railt\Parser\Environment;
use Railt\Parser\Parser as LLParser;
use Railt\Parser\ParserInterface;
use Railt\Parser\Rule\Alternation;
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Repetition;
use Railt\Parser\Rule\Token;

/**
 * Class Parser
 */
class Parser implements ParserInterface
{
    /**
     * @var string[]
     */
    private const DELEGATES = [
        'Production'    => Delegate\Production\ConcatenationDelegate::class,    // Concat
        'Concatenation' => Delegate\Production\ConcatenationDelegate::class,    // Concat
        'Repetition'    => Delegate\Production\RepetitionDelegate::class,       // Repeat
        'Alternation'   => Delegate\Production\AlternationDelegate::class,      // Choice
        'Invocation'    => Delegate\Production\InvocationDelegate::class,       // Terminal
        'Pragma'        => Delegate\PragmaDefinitionDelegate::class,
        'Include'       => Delegate\IncludeDelegate::class,
        'Rule'          => Delegate\RuleDefinitionDelegate::class,
        'Token'         => Delegate\TokenDefinitionDelegate::class,
    ];

    /**
     * @var ParserInterface
     */
    private $llk;

    /**
     * Parser constructor.
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->llk = new LLParser(new Lexer(), $this->rules(), $this->options());

        $this->llk->addDelegates(self::DELEGATES);
    }

    /**
     * @return array
     */
    private function rules(): array
    {
        return [
            new Repetition(0, 0, -1, [2]),
            new Concatenation(1, [0], 'Grammar'),
            new Alternation(2, [7, 9, 11, 12]),
            new Token(3, 'T_TOKEN'),
            new Concatenation(4, [3], 'TokenDefinition'),
            new Token(5, 'T_SKIP'),
            new Concatenation(6, [5], 'TokenDefinition'),
            new Alternation(7, [4, 6]),
            new Token(8, 'T_PRAGMA'),
            new Concatenation(9, [8], 'PragmaDefinition'),
            new Token(10, 'T_INCLUDE'),
            new Concatenation(11, [10], 'IncludeDefinition'),
            new Concatenation(12, [16, 26], 'RuleDefinition'),
            new Repetition(13, 0, 1, [21]),
            new Token(14, 'T_NAME'),
            new Repetition(15, 0, 1, [19]),
            new Concatenation(16, [13, 14, 15, 24], 'RuleName'),
            new Token(17, 'T_DELEGATE', false),
            new Token(18, 'T_NAME'),
            new Concatenation(19, [17, 18], 'RuleDelegate'),
            new Token(20, 'T_KEPT_NAME', false),
            new Concatenation(21, [20], 'ShouldKeep'),
            new Token(22, 'T_COLON', false),
            new Token(23, 'T_EQ', false),
            new Alternation(24, [22, 23]),
            new Repetition(25, 1, -1, [41]),
            new Concatenation(26, [25], 'RuleProduction'),
            new Alternation(27, [36, 40]),
            new Token(28, 'T_TOKEN_SKIPPED'),
            new Concatenation(29, [28], 'RuleProductionTerminal'),
            new Token(30, 'T_TOKEN_KEPT'),
            new Concatenation(31, [30], 'RuleProductionTerminal'),
            new Token(32, 'T_INVOKE'),
            new Concatenation(33, [32], 'RuleProductionTerminal'),
            new Token(34, 'T_TOKEN_STRING'),
            new Concatenation(35, [34], 'RuleProductionTerminal'),
            new Alternation(36, [29, 31, 33, 35]),
            new Token(37, 'T_GROUP_OPEN', false),
            new Repetition(38, 1, -1, [41]),
            new Token(39, 'T_GROUP_CLOSE', false),
            new Concatenation(40, [37, 38, 39], 'RuleGroup'),
            new Alternation(41, [48, 51, 50, 44]),
            new Token(42, 'T_KEPT_NAME', false),
            new Token(43, 'T_NAME'),
            new Concatenation(44, [42, 43], 'RuleRename'),
            new Token(45, 'T_OR', false),
            new Concatenation(46, [45, 27], 'RuleProductionAlternation'),
            new Repetition(47, 1, -1, [46]),
            new Concatenation(48, [27, 47]),
            new Repetition(49, 1, -1, [27]),
            new Concatenation(50, [49], 'RuleProductionConcatenation'),
            new Concatenation(51, [27, 59], 'RuleProductionRepetition'),
            new Token(52, 'T_ZERO_OR_ONE'),
            new Concatenation(53, [52], 'RepetitionInterval'),
            new Token(54, 'T_ONE_OR_MORE'),
            new Concatenation(55, [54], 'RepetitionInterval'),
            new Token(56, 'T_ZERO_OR_MORE'),
            new Concatenation(57, [56], 'RepetitionInterval'),
            new Concatenation(58, [63], 'RepetitionInterval'),
            new Alternation(59, [53, 55, 57, 58]),
            new Token(60, 'T_BLOCK_OPEN', false),
            new Alternation(61, [67, 69]),
            new Token(62, 'T_BLOCK_CLOSE', false),
            new Concatenation(63, [60, 61, 62]),
            new Repetition(64, 0, 1, [71]),
            new Token(65, 'T_COMMA', false),
            new Repetition(66, 0, 1, [73]),
            new Concatenation(67, [64, 65, 66]),
            new Token(68, 'T_NUMBER'),
            new Concatenation(69, [68], 'Repeat'),
            new Token(70, 'T_NUMBER'),
            new Concatenation(71, [70], 'From'),
            new Token(72, 'T_NUMBER'),
            new Concatenation(73, [72], 'To'),
        ];
    }

    /**
     * @return array
     */
    private function options(): array
    {
        return [
            Configuration::PRAGMA_ROOT      => 'Grammar',
            Configuration::PRAGMA_LOOKAHEAD => 1024,
        ];
    }

    /**
     * @param Readable $input
     * @param Environment|null $env
     * @return RuleInterface
     * @throws \LogicException
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Parser\Exception\UnrecognizedRuleException
     */
    public function parse(Readable $input, Environment $env = null): RuleInterface
    {
        return $this->llk->parse($input, $env);
    }
}
