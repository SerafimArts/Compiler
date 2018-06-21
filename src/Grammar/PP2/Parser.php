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
     * @var ParserInterface
     */
    private $llk;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->llk = new LLParser(new Lexer(), $this->rules(), $this->options());
    }

    /**
     * @return array
     */
    private function rules(): array
    {
        return [
            // 0
            new Repetition(1, 0, -1, [6], 'Grammar'),
            new Token(2, 'T_PRAGMA', true),
            new Token(3, 'T_TOKEN', true),
            new Token(4, 'T_SKIP', true),
            new Token(5, 'T_INCLUDE', true),
            new Alternation(6, [2, 3, 4, 5, 7], null),
            new Concatenation(7, [12, 19], 'Rule'),
            new Repetition(8, 0, 1, [17], null),
            new Token(9, 'T_NAME', true),
            new Repetition(10, 0, 1, [15], null),
            new Token(11, 'T_COLON', false),
            new Concatenation(12, [8, 9, 10, 11], 'Name'),
            new Token(13, 'T_DELEGATE', false),
            new Token(14, 'T_NAME', true),
            new Concatenation(15, [13, 14], 'Delegate'),
            new Token(16, 'T_KEPT_NAME', false),
            new Concatenation(17, [16], 'ShouldKeep'),
            new Repetition(18, 1, -1, [20], null),
            new Concatenation(19, [18], 'Production'),
            new Alternation(20, [33, 66, 36, 24, 23], null),
            new Token(21, 'T_KEPT_NAME', false),
            new Token(22, 'T_NAME', true),
            new Concatenation(23, [21, 22], 'Rename'),
            new Alternation(24, [32, 28], null),
            new Token(25, 'T_GROUP_OPEN', false),
            new Repetition(26, 1, -1, [20], null),
            new Token(27, 'T_GROUP_CLOSE', false),
            new Concatenation(28, [25, 26, 27], null),
            new Token(29, 'T_KEPT', true),
            new Token(30, 'T_SKIPPED', true),
            new Token(31, 'T_INVOKE', true),
            new Alternation(32, [29, 30, 31], null),
            new Repetition(33, 2, -1, [24], 'Concatenation'),
            // 34
            new Repetition(35, 1, -1, [24], null),
            new Concatenation(36, [35, 44], 'Repetition'),
            new Token(37, 'T_ZERO_OR_ONE', true),
            new Concatenation(38, [37], 'RepetitionInterval'),
            new Token(39, 'T_ONE_OR_MORE', true),
            new Concatenation(40, [39], 'RepetitionInterval'),
            new Token(41, 'T_ZERO_OR_MORE', true),
            new Concatenation(42, [41], 'RepetitionInterval'),
            new Concatenation(43, [48], 'RepetitionInterval'),
            new Alternation(44, [38, 40, 42, 43], null),
            new Token(45, 'T_REPETITION_OPEN', false),
            new Alternation(46, [52, 54], null),
            new Token(47, 'T_REPETITION_CLOSE', false),
            new Concatenation(48, [45, 46, 47], null),
            new Repetition(49, 0, 1, [56], null),
            new Token(50, 'T_COMMA', false),
            new Repetition(51, 0, 1, [58], null),
            new Concatenation(52, [49, 50, 51], null),
            new Token(53, 'T_NUMBER', true),
            new Concatenation(54, [53], 'Repeat'),
            new Token(55, 'T_NUMBER', true),
            new Concatenation(56, [55], 'From'),
            new Token(57, 'T_NUMBER', true),
            new Concatenation(58, [57], 'To'),
            new Concatenation(59, [33], 'Alternation'),
            new Concatenation(60, [24], 'Alternation'),
            new Alternation(61, [59, 60], null),
            new Token(62, 'T_OR', false),
            new Alternation(63, [33, 24], null),
            new Concatenation(64, [62, 63], null),
            new Repetition(65, 1, -1, [64], null),
            new Concatenation(66, [61, 65], null),
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
     * @return RuleInterface
     * @throws \LogicException
     * @throws \Railt\Io\Exception\ExternalFileException
     * @throws \Railt\Parser\Exception\UnrecognizedRuleException
     */
    public function parse(Readable $input): RuleInterface
    {
        return $this->llk->parse($input);
    }
}
