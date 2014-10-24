<?php

namespace Rde;

// array

function is_array($arg)
{
    return  \is_array($arg) || \is_a($arg, 'ArrayAccess');
}

function array_get($arr, $key, $default = null)
{
    if ( ! is_array($arr)) {
        throw new \InvalidArgumentException('參數1必須為陣列或ArrayAccess實體');
    }

    if (array_key_exists($key, $arr)) {
        return $arr[$key];
    }

    if ($default && is_callable($default)) {
        return $default();
    }

    return $default;
}

function array_each($callback)
{
    foreach (array_slice(func_get_args(), 1) as $i => $arr) {
        if ( ! is_array($arr)) {
            throw new \InvalidArgumentException("參數".($i+2)."必須為陣列或ArrayAccess實體");
        }
        foreach ($arr as $k => $v) {
            if (false === $callback($k, $v)) {
                break;
            }
        }
    }
}

function array_merge_callback($driver, array $base)
{
    $collection = $base;
    $appends = array_slice(func_get_args(), 2);

    foreach ($appends as $append) {
        foreach ($append as $k => $v) {
            $driver(
                $v,
                $k,
                $collection,
                $driver);
        }
    }

    return $collection;
}

// tool
function call($callable, array $args = array())
{
    switch (count($args)) {
        case 0: return call_user_func($callable);
        case 1: return call_user_func($callable, $args[0]);
        case 2: return call_user_func($callable, $args[0], $args[1]);
        case 3: return call_user_func($callable, $args[0], $args[1], $args[2]);
        case 4: return call_user_func($callable, $args[0], $args[1], $args[2], $args[3]);
    }

    return call_user_func_array($callable, $args);
}

function pipeline($commands, $arg, $accept = null)
{
    $accept = is_callable($accept) ? $accept : function($v){return (bool) $v;};
    $commands = is_array($commands) ? $commands : preg_split('/\|/', $commands);
    foreach ($commands as $command) {
        $arg = call_user_func($command, $arg);
        if ( ! call_user_func($accept, $arg)) {
            break;
        }
    }

    return $arg;
}

function value($val)
{
    return is_callable($val) ? $val() : $val;
}

function with($any)
{
    return $any;
}

// debug
function dd()
{
    call_user_func_array('var_dump', func_get_args());
    die;
}
