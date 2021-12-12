#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    return map(fn($i)=>explode('-', $i), file('input.txt', FILE_IGNORE_NEW_LINES));
}

function close(string $entrance, array $paths) : array
{
    return filter($paths, fn($i)=> $i[0] !== $entrance && $i[1] !== $entrance);
}

function small(string $cave) : bool
{
    return ctype_lower($cave);
}

function exits(string $entrance, array $paths) : array
{
    return reduce($paths, function($c,$v) use ($entrance) {
        if ($entrance === $v[0]) $c[] = $v[1];
        if ($entrance === $v[1]) $c[] = $v[0];
        return $c;
    }, []);
}

function explore($entrance, $paths) : int
{
    if ($entrance === 'end') return 1;

    $count = 0;

    $exits = exits($entrance, $paths);

    if (small($entrance)) {
        $paths = close($entrance, $paths);
    }

    foreach($exits as $exit) {
        $count+=explore($exit, $paths);
    }

    return $count;
}

$caves = load();
$sum = explore('start', $caves);
output($sum);
