<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('bootstrap/cache')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->notName('_ide_helper.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@Symfony'                               => true,
            'array_syntax'                           => ['syntax' => 'short'],
            'linebreak_after_opening_tag'            => true,
            'not_operator_with_successor_space'      => true,
            'ordered_imports'                        => true,
            'phpdoc_order'                           => true,
            'yoda_style'                             => false,
            'phpdoc_separation'                      => false,
            'phpdoc_summary'                         => false,
            'no_empty_phpdoc'                        => false,
            'phpdoc_no_empty_return'                 => false,
            'no_superfluous_phpdoc_tags'             => false,
            'phpdoc_align'                           => [
                'align' => 'vertical',
            ],
            'declare_strict_types'                   => false,
            'phpdoc_no_package'                      => false,
            'binary_operator_spaces'                 => ['operators' => ['=>' => 'align', '=' => 'align']],
            'trailing_comma_in_multiline'            => false,
            'ordered_class_elements'                 => false,
            'php_unit_method_casing'                 => false,
            'php_unit_test_case_static_method_calls' => false,
        ]
    )
    ->setFinder($finder);
