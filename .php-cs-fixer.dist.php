<?php

if (PHP_SAPI !== 'cli') {
    die('This script supports command line usage only. Please check your command.');
}

// Define in which folders to search and which files to exclude
$finder = (new \PhpCsFixer\Finder())
    ->in(__DIR__)
    ->ignoreVCSIgnored(true)
    ->path(['packages', 'public/typo3conf'])
    ->notName('ext_emconf.php');

return (new \PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@DoctrineAnnotation' => true,
            '@PSR2' => true,
            'array_indentation' => true,
            'array_syntax' => ['syntax' => 'short'],
            'blank_line_after_opening_tag' => true,
            'blank_line_before_statement' => ['statements' => ['return']],
            'braces' => ['allow_single_line_closure' => true],
            'binary_operator_spaces' => ['operators' => ['=>' => 'single_space']],
            'cast_spaces' => ['space' => 'none'],
            'class_attributes_separation' => ['elements' => ['property' => 'one', 'trait_import' => 'one']],
            'clean_namespace' => true,
            'combine_consecutive_issets' => true,
            'compact_nullable_typehint' => true,
            'concat_space' => ['spacing' => 'one'],
            'declare_equal_normalize' => ['space' => 'none'],
            'dir_constant' => true,
            'function_typehint_space' => true,
            'general_phpdoc_tag_rename' => [
                'replacements' => ['inheritDocs' => 'inheritDoc', 'inheritdocs' => 'inheritDoc', 'inheritdoc' => 'inheritDoc'],
                'case_sensitive' => true,
            ],
            'list_syntax' => ['syntax' => 'short'],
            'logical_operators' => true,
            'lowercase_cast' => true,
            'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
            'method_chaining_indentation' => true,
            'modernize_types_casting' => true,
            'native_function_casing' => true,
            'new_with_braces' => true,
            'no_alias_functions' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_empty_phpdoc' => true,
            'no_empty_statement' => true,
            'no_extra_blank_lines' => true,
            'no_leading_import_slash' => true,
            'no_leading_namespace_whitespace' => true,
            'no_null_property_initialization' => true,
            'no_short_bool_cast' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_spaces_around_offset' => true,
            'no_superfluous_elseif' => true,
            'no_trailing_comma_in_singleline_array' => true,
            'no_unneeded_control_parentheses' => true,
            'no_unused_imports' => true,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'no_whitespace_before_comma_in_array' => true,
            'no_whitespace_in_blank_line' => true,
            'ordered_imports' => true,
            'php_unit_construct' => ['assertions' => ['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']],
            'php_unit_mock_short_will_return' => true,
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
            'phpdoc_no_access' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_no_package' => true,
            'phpdoc_order' => true,
            'phpdoc_scalar' => true,
            'phpdoc_trim' => true,
            'phpdoc_types' => true,
            'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
            'phpdoc_var_annotation_correct_order' => true,
            'phpdoc_var_without_name' => true,
            'protected_to_private' => true,
            'return_type_declaration' => ['space_before' => 'none'],
            'short_scalar_cast' => true,
            'single_blank_line_before_namespace' => true,
            'single_quote' => true,
            'single_line_comment_style' => ['comment_types' => ['hash']],
            'single_trait_insert_per_statement' => true,
            'trailing_comma_in_multiline' => true,
            'trim_array_spaces' => true,
            'void_return' => true,
            'whitespace_after_comma_in_array' => true,
        ]
    )
    ->setFinder($finder);
