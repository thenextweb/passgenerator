<?php
// vim: set ft=php:

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('storage')
    ->exclude('node_modules')
    ->exclude('public')
    ->exclude('resources')
    ->exclude('vendor')
    ->exclude('bootstrap')
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        // exclude these (part of PSR 2)
        '-psr0', // app => App
        '-single_line_after_imports', // this drops lines between use statements

        // include these
        'blankline_after_open_tag',
        // 'concat_without_spaces',
        'double_arrow_multiline_whitespaces',
        'duplicate_semicolon',
        'empty_return',
        'extra_empty_lines',
        'function_typehint_space',
        'include',
        'indentation',
        'join_function',
        'list_commas',
        'multiline_array_trailing_comma',
        'namespace_no_leading_whitespace',
        'new_with_braces',
        // 'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs',
        'object_operator',
        'operators_spaces',
        // 'phpdoc_indent',
        // 'phpdoc_inline_tag',
        // 'phpdoc_no_access',
        // 'phpdoc_no_empty_return',
        // 'phpdoc_no_package',
        // 'phpdoc_params',
        // 'phpdoc_scalar',
        // 'phpdoc_separation',
        // 'phpdoc_short_description',
        // 'phpdoc_to_comment',
        // 'phpdoc_trim',
        // 'phpdoc_type_to_var',
        // 'phpdoc_types',
        // 'phpdoc_var_without_name',
        // 'pre_increment',
        'remove_leading_slash_use',
        // 'remove_lines_between_uses',
        // 'return',
        'self_accessor',
        'single_array_no_trailing_comma',
        'single_blank_line_before_namespace',
        'single_quote',
        'spaces_before_semicolon',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'trim_array_spaces',
        // 'unalign_double_arrow',
        // 'unalign_equals',
        'unary_operators_spaces',
        // 'unused_use',
        'whitespacy_lines',
        // 'align_double_arrow',
        // 'align_equals',
        'concat_with_spaces',
        'ereg_to_preg',
        // 'header_comment',
        // 'logical_not_operators_with_spaces',
        // 'logical_not_operators_with_successor_space',
        // 'long_array_syntax',
        'multiline_spaces_before_semicolon',
        'newline_after_open_tag',
        // 'no_blank_lines_before_namespace',
        // 'ordered_use',
        // 'php4_constructor',
        'php_unit_construct',
        'php_unit_strict',
        'phpdoc_order',
        // 'phpdoc_var_to_type',
        'short_array_syntax',
        'short_echo_tag',
        // 'strict',
        // 'strict_param',
    ])
    ->finder($finder);
