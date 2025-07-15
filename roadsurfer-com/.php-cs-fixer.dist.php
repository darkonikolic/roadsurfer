<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_to_property_type' => true,
        'readonly_class' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
