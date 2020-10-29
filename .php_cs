<?php

// vim: set ft=php:

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => true,
        'binary_operator_spaces' => true,
        'cast_spaces' => true,
        'concat_space' => ['spacing' => 'one'],
        'fully_qualified_strict_types' => true,
        'function_typehint_space' => true,
        'global_namespace_import' => [
                'import_classes' => null,
                'import_constants' => false,
                'import_functions' => true,
        ],
        'include' => true,
        'indentation_type' => true,
        'linebreak_after_opening_tag' => true,
        'new_with_braces' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_leading_namespace_whitespace' => true,
        'no_leading_import_slash' => true,
        'no_short_echo_tag' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'simplified_null_return' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_after_imports' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
    ])
    ->setFinder($finder);
