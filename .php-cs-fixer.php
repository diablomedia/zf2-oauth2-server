<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->files()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();

return $config->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'binary_operator_spaces' => ['operators' => ['=' => 'align', '=>' => 'align']],
        'single_quote' => false,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'dir_constant' => true
    ])
    ->setUsingCache(true)
    ->setFinder($finder);
;
