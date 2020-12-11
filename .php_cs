<?php

declare(strict_types = 1);

$finder = PhpCsFixer\Finder::create()
	->notPath('docs')
	->notPath('vendor')
	->notPath('public')
	->notPath('resources')
	->notPath('db')
	->notPath('writable')
	->in(__DIR__)
	->name('*.php')
	->ignoreDotFiles(true)
;

$config = PhpCsFixer\Config::create()
	->setRiskyAllowed(true)
	->setRules([
		'@PhpCsFixer'             => true,
		'align_multiline_comment' => [
			'comment_type' => 'phpdocs_like',
		],
		'array_syntax' => [
			'syntax' => 'short',
		],
		'binary_operator_spaces' => [
			'operators' => [
				'=>' => 'align',
			],
		],
		'braces' => [
			'position_after_functions_and_oop_constructs' => 'next',
		],
		'blank_line_after_opening_tag' => false,
		'blank_line_before_statement'  => true,
		'cast_spaces'                  => true,
		'class_attributes_separation'  => true,
		'compact_nullable_typehint'    => true,
		'concat_space'                 => [
			'spacing' => 'one',
		],
		'declare_equal_normalize' => [
			'space' => 'single',
		],
		'explicit_indirect_variable'          => true,
		'function_typehint_space'             => true,
		'general_phpdoc_annotation_remove'    => [],
		'indentation_type'                    => true,
		'native_function_invocation'          => true,
		'no_alternative_syntax'               => false,
		'no_blank_lines_after_class_opening'  => true,
		'no_blank_lines_after_phpdoc'         => true,
		'no_extra_consecutive_blank_lines'    => true,
		'no_mixed_echo_print'                 => true,
		'no_php4_constructor'                 => true,
		'no_short_echo_tag'                   => false,
		'no_unused_imports'                   => false,
		'no_useless_else'                     => true,
		'no_useless_return'                   => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line'         => true,
		'object_operator_without_whitespace'  => true,
		'ordered_class_elements'              => true,
		'ordered_imports'                     => true,
		'phpdoc_add_missing_param_annotation' => true,
		'phpdoc_align'                        => true,
		'phpdoc_indent'                       => true,
		'phpdoc_order'                        => true,
		'protected_to_private'                => false,
		'return_type_declaration'             => true,
		'self_accessor'                       => true,
		'semicolon_after_instruction'         => false,
		'single_blank_line_before_namespace'  => true,
		'single_quote'                        => true,
		'ternary_operator_spaces'             => true,
		'ternary_to_null_coalescing'          => true,
		'visibility_required'                 => true,
		'whitespace_after_comma_in_array'     => true,
		'yoda_style'                          => true,
	])
	->setIndent("\t")
	->setFinder($finder)
;

return $config;
