<?php
/**
 * Quack Compiler and toolkit
 * Copyright (C) 2015-2017 Quack and CONTRIBUTORS
 *
 * This file is part of Quack.
 *
 * Quack is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Quack is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Quack.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace QuackCompiler\Lexer;

class SymbolDecypher
{
    public static function __callStatic($method, $args)
    {
        list ($context) = $args;

        switch ($method) {
            case '<':
                return static::tryMatch($context, ['<<', '<>', '<=']);
            case '>':
                return static::tryMatch($context, ['>>', '>=', '>>']);
            case ':':
                return static::tryMatch($context, ['::', ':-']);
            case '*':
                return static::tryMatch($context, ['**']);
            case '=':
                return static::tryMatch($context, ['=~']);
            case '|':
                return static::tryMatch($context, ['|>']);
            case '&':
                return static::tryMatch($context, ['&{', '&(']);
            case '.':
                return static::tryMatch($context, ['..']);
            case '#':
                return static::tryMatch($context, ['#{', '#(']);
            case '%':
                return static::tryMatch($context, ['%{']);
            default:
                return static::fetch($context, $context->peek);
        }
    }

    private static function tryMatch($context, $operator_list)
    {
        foreach ($operator_list as $operator) {
            if ($context->matches($operator)) {
                return static::fetch($context, $operator);
            }
        }

        return static::fetch($context, $context->peek);
    }

    private static function fetch($context, $symbol)
    {
        $size = strlen($symbol);
        $context->consume($size);
        $context->column += $size;
        return new Token($symbol);
    }
}
