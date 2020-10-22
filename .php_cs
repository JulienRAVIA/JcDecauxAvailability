<?php

require(__DIR__ .'/vendor/autoload.php');

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;
