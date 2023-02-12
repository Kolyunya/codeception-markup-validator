<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = Finder::create()
    ->in(sprintf('%s/sources', __DIR__))
    ->in(sprintf('%s/tests', __DIR__))
    ->name('*.php')
    ->files()
;

$config = (new Config())
    ->setRules(array(
        '@PSR1' => true,
        '@PSR2' => true,
        'array_syntax' => array(
            'syntax' => 'long',
        ),
        'no_trailing_whitespace' => true,
        'ordered_imports' => array(
            'imports_order' => null,
        ),
        'single_blank_line_at_eof' => true,
        'strict_param' => true,
    ))
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;

return $config;
