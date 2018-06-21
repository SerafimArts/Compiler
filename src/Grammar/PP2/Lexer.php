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
    public const T_WHITESPACE       = 'T_WHITESPACE';
    public const T_COMMENT          = 'T_COMMENT';
    public const T_BLOCK_COMMENT    = 'T_BLOCK_COMMENT';
    public const T_PRAGMA           = 'T_PRAGMA';
    public const T_INCLUDE          = 'T_INCLUDE';
    public const T_TOKEN            = 'T_TOKEN';
    public const T_SKIP             = 'T_SKIP';
    public const T_OR               = 'T_OR';
    public const T_ZERO_OR_ONE      = 'T_ZERO_OR_ONE';
    public const T_ONE_OR_MORE      = 'T_ONE_OR_MORE';
    public const T_ZERO_OR_MORE     = 'T_ZERO_OR_MORE';
    public const T_SKIPPED          = 'T_SKIPPED';
    public const T_KEPT             = 'T_KEPT';
    public const T_INVOKE           = 'T_INVOKE';
    public const T_GROUP_OPEN       = 'T_GROUP_OPEN';
    public const T_GROUP_CLOSE      = 'T_GROUP_CLOSE';
    public const T_REPETITION_OPEN  = 'T_REPETITION_OPEN';
    public const T_REPETITION_CLOSE = 'T_REPETITION_CLOSE';
    public const T_COMMA            = 'T_COMMA';
    public const T_NUMBER           = 'T_NUMBER';
    public const T_KEPT_NAME        = 'T_KEPT_NAME';
    public const T_NAME             = 'T_NAME';
    public const T_COLON            = 'T_COLON';
    public const T_DELEGATE         = 'T_DELEGATE';
    /**#@-*/

    /**
     * @var array|string[] Tokens list
     */
    private const TOKENS_LIST = [
        self::T_WHITESPACE       => '(\\xfe\\xff|\\x20|\\x09|\\x0a|\\x0d)+',
        self::T_COMMENT          => '//[^\\n]*',
        self::T_BLOCK_COMMENT    => '/\\*.*?\\*/',
        self::T_PRAGMA           => '%pragma\\h+([\w\\.]+)\\h+([^\s]+)',
        self::T_INCLUDE          => '%include\\h+([^\s]+)',
        self::T_TOKEN            => '%token\\h+(\w+)\\h+([^\s]+)',
        self::T_SKIP             => '%skip\\h+(\w+)\\h+([^\s]+)',
        self::T_OR               => '\\|',
        self::T_ZERO_OR_ONE      => '\\?',
        self::T_ONE_OR_MORE      => '\\+',
        self::T_ZERO_OR_MORE     => '\\*',
        self::T_SKIPPED          => '::(\w+)::',
        self::T_KEPT             => '<(\w+)>',
        self::T_INVOKE           => '(\w+)\\(\\)',
        self::T_GROUP_OPEN       => '\\(',
        self::T_GROUP_CLOSE      => '\\)',
        self::T_REPETITION_OPEN  => '{',
        self::T_REPETITION_CLOSE => '}',
        self::T_COMMA            => ',',
        self::T_NUMBER           => '\d+',
        self::T_KEPT_NAME        => '#',
        self::T_NAME             => '[a-zA-Z_\\x7f-\\xff\\\\][a-zA-Z0-9_\\x7f-\\xff\\\\]*',
        self::T_COLON            => ':',
        self::T_DELEGATE         => '\\->',
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
