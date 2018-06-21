<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Grammar\PP2;

use Railt\Lexer\Driver\NativeStateless;

/**
 * Class Lexer
 */
class Lexer extends NativeStateless
{
    /**@#+
     * List of tokens used inside grammar files.
     */
    public const T_WHITESPACE      = 'T_WHITESPACE';
    public const T_COMMENT         = 'T_COMMENT';
    public const T_BLOCK_COMMENT   = 'T_BLOCK_COMMENT';
    public const T_PRAGMA          = 'T_PRAGMA';
    public const T_TOKEN           = 'T_TOKEN';
    public const T_SKIP            = 'T_SKIP';
    public const T_INCLUDE         = 'T_INCLUDE';
    public const T_NODE_DEFINITION = 'T_NODE_DEFINITION';
    public const T_OR              = 'T_OR';
    public const T_ZERO_OR_ONE     = 'T_ZERO_OR_ONE';
    public const T_ONE_OR_MORE     = 'T_ONE_OR_MORE';
    public const T_ZERO_OR_MORE    = 'T_ZERO_OR_MORE';
    public const T_N_TO_M          = 'T_N_TO_M';
    public const T_ZERO_TO_M       = 'T_ZERO_TO_M';
    public const T_N_OR_MORE       = 'T_N_OR_MORE';
    public const T_EXACTLY_N       = 'T_EXACTLY_N';
    public const T_SKIPPED         = 'T_SKIPPED';
    public const T_KEPT            = 'T_KEPT';
    public const T_INVOKE          = 'T_INVOKE';
    public const T_RENAME          = 'T_RENAME';
    public const T_GROUP_OPEN      = 'T_GROUP_OPEN';
    public const T_GROUP_CLOSE     = 'T_GROUP_CLOSE';
    /**#@-*/

    /**
     * @var array|string[] Tokens list
     */
    private const TOKENS_LIST = [
        self::T_WHITESPACE      => '\s+',
        self::T_COMMENT         => '//[^\\n]*',
        self::T_BLOCK_COMMENT   => '/\\*.*?\\*/',
        self::T_PRAGMA          => '%pragma\h+([\w\.]+)\h+(.+?)\s+',
        self::T_TOKEN           => '%token\h+(\w+)\h+(.+?)(?:\s*->\h*\\$(\d+)\h*)?\n',
        self::T_SKIP            => '%skip\h+(\w+)\h+(.+?)\s+',
        self::T_INCLUDE         => '%include\h+(.+?)\s+',
        self::T_NODE_DEFINITION => '(#?\w+)(?:\s*->\h*(.+?)\h*)?\s*:',
        self::T_OR              => '\\|',
        self::T_ZERO_OR_ONE     => '\\?',
        self::T_ONE_OR_MORE     => '\\+',
        self::T_ZERO_OR_MORE    => '\\*',
        self::T_N_TO_M          => '{\h*(\d+),\h*(\d+)\h*}',
        self::T_ZERO_TO_M       => '{\h*,\h*(\d+)\h*}',
        self::T_N_OR_MORE       => '{\h*(\d+)\h*,\h*}',
        self::T_EXACTLY_N       => '{(\d+)}',
        self::T_SKIPPED         => '::(\w+)::',
        self::T_KEPT            => '<(\w+)>',
        self::T_INVOKE          => '(\w+)\\(\\)',
        self::T_RENAME          => '#(\w+)',
        self::T_GROUP_OPEN      => '\\(',
        self::T_GROUP_CLOSE     => '\\)',
    ];

    /**
     * A list of skipped tokens
     */
    private const TOKENS_SKIP = [
        self::T_WHITESPACE,
        self::T_COMMENT,
        self::T_BLOCK_COMMENT,
    ];

    /**
     * Lexer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        foreach (self::TOKENS_LIST as $name => $pcre) {
            $this->add($name, $pcre, \in_array($name, self::TOKENS_SKIP, true));
        }
    }
}
