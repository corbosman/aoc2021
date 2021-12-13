#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    return map(fn($i)=>explode('-', $i), file('input.txt', FILE_IGNORE_NEW_LINES));
}

function study(array $tunnels) : array
{
    return reduce($tunnels, fn($c, $v)=>merge($c, [$v[0] => 0], [$v[1] => 0]), []);
}

function exits(string $entrance, array $tunnels) : array
{
    return reduce($tunnels, function($c,$v) use ($entrance) {
        if ($entrance === $v[0]) $c[] = $v[1];
        if ($entrance === $v[1]) $c[] = $v[0];
        return $c;
    }, []);
}

function small_caves(array $caves) : array
{
    return filter($caves, fn($cave) => !in_array($cave, ['start', 'end']) && ctype_lower($cave), ARRAY_FILTER_USE_KEY);
}

function is_large_cave(string $cave) : bool
{
    return ctype_upper($cave);
}

function is_small_cave(string $cave): bool
{
    return ctype_lower($cave) && $cave !== 'start';
}

function visits(string $cave, array $caves) : int
{
    return $caves[$cave];
}

function visited(string $cave, array $caves) : bool
{
    return visits($cave, $caves) !== 0;
}

function double_visited(array $caves) : bool
{
    foreach($caves as $cave => $visits) {
        if ($visits === 2) return true;
    }
    return false;
}

function can_enter(string $exit, array $caves, bool $twice) : bool
{
    if ($exit === 'start')  return false;                  // cant return back to start
    if (is_large_cave($exit)) return true;                 // can enter a large cave
    if (!visited($exit, $caves)) return true;              // can enter a small cave we haven't visited
    if (!$twice) return true;                              // can enter a small cave if we haven't visited one twice before

    return false;
}

function explore(string $entrance, array $tunnels, array $caves, bool $twice = false) : int
{
    // end of the cave, we have a full path!
    if ($entrance === 'end') return 1;

    // register a new visit to this cave
    $caves[$entrance] += 1;

    // did we visit a small cave twice?
    if (!$twice && $caves[$entrance] === 2 && is_small_cave($entrance)) {
        $twice = true;
    }

    // try to continue on into the caves
    return reduce(exits($entrance, $tunnels), function($count, $exit)  use ($tunnels, $caves, $twice) : int {
        if (can_enter($exit, $caves, $twice)) {
            $count += explore($exit, $tunnels, $caves, $twice);
        }
        return $count;
    }, 0);
}

$tunnels = load();
$caves   = study($tunnels);
$count   = explore('start', $tunnels, $caves, false);

output($count);
