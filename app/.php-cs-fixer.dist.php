<?php

declare(strict_types=1);

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('var')
;

return new PhpCsFixer\Config()
    ->setRules([
        '@Symfony' => true,
        'single_line_throw' => false,
        'phpdoc_align' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder)
;
