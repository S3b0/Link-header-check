<?php

declare(strict_types=1);

/** @var string|null $proxy */
$proxy = getenv('PROXY') ?: null;

$classes = [
    1 => 'secondary',
    2 => 'success',
    3 => 'secondary',
    4 => 'warning',
    5 => 'alert'
];
