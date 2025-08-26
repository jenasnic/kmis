<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('migrations')
    ->exclude('var')
    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in('.')
;

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@PHP82Migration' => true,
        '@DoctrineAnnotation' => true,
        'dir_constant' => true,
        'no_superfluous_phpdoc_tags' => [
            'remove_inheritdoc' => false,
        ],
        'nullable_type_declaration_for_default_null_value' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'phpdoc_align' => false,
        'phpdoc_no_package' => false,
        'phpdoc_to_comment' => ['ignored_tags' => ['todo', 'var']],
    ])
    ->setFinder($finder)
    ;
