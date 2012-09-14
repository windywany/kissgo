<?php
/**
 * 插件基本库.
 *
 *
 * 提供插件功能,大部实现来自wordpress.
 *
 * @author Leo Ning <leo.ning@like18.com>
 * @copyright LIKE18 INC. 2008 - 2011
 * @version 1.0
 * @since 1.0
 * @package core
 * @subpackage plugin
 */
/**
 * 运行时插件
 *
 * @global array
 * @name $rtk_hooks
 * @var array
 */
$rtk_hooks = array();
/**
 * 已经排序的插件
 *
 * @global array
 * @name $sorted_hooks
 * @var array
 */
$sorted_hooks = array();
/**
 * 已经触发的HOOKS
 * @global array
 * @name $triggered_hooks
 * @var array
 */
$triggered_hooks = array();
/**
 * 正在触发的HOOKS
 *
 * @global array
 * @name $triggering_hooks
 * @var array
 */
$triggering_hooks = array();
/**
 * 注册一个HOOK的回调函数
 *
 * @global array 运行时插件回调
 * @global array 已排序插件
 * @param string $hook_name HOOK名称
 * @param mixed $hook_func 回调函数
 * @param int $priority 优先级
 * @param int $accepted_args 接受参数个数
 */
function bind($hook, $hook_func, $priority = 10, $accepted_args = 1) {
    global $rtk_hooks, $sorted_hooks;

    if (empty ($hook)) {
        trigger_error('the hook name must not be empty!', E_USER_ERROR);
    }

    if (empty ($hook_func)) {
        trigger_error('the hook function must not be empty!', E_USER_ERROR);
    }

    $idx = __rt_hook_unique_id($hook, $hook_func, $priority);

    $rtk_hooks [$hook] [$priority] [$idx] = array('func' => $hook_func, 'accepted_args' => $accepted_args);

    unset ($sorted_hooks [$hook]);

    return true;
}

/**
 * 移除一个HOOK回调函数
 *
 * @global array 运行时插件回调
 * @global array 已排序插件
 * @param string $hook_name HOOK名称
 * @param mixed $hook_func 回调函数
 * @param int $priority 优先级
 * @return boolean
 */
function unbind($hook, $hook_func, $priority = 10) {
    global $rtk_hooks, $sorted_hooks;

    $idx = __rt_hook_unique_id($hook, $hook_func, $priority);

    $r = isset ($rtk_hooks [$hook] [$priority] [$idx]);

    if (true === $r) {
        unset ($rtk_hooks [$hook] [$priority] [$idx]);
        if (empty ($rtk_hooks [$hook] [$priority])) {
            unset ($rtk_hooks [$hook] [$priority]);
        }
        unset ($sorted_hooks [$hook]);
    }
    return $r;
}

/**
 * 移除$hook对应的所有回调函数
 *
 * @global array 运行时插件回调
 * @global array 已排序插件
 * @param string $hook_name HOOK名称
 * @param int $priority 优先级
 */
function unbind_all($hook, $priority = false) {
    global $rtk_hooks, $sorted_hooks;

    if (isset ($rtk_hooks [$hook])) {
        if (false !== $priority && isset ($$rtk_hooks [$hook] [$priority])) {
            unset ($rtk_hooks [$hook] [$priority]);
        } else {
            unset ($rtk_hooks [$hook]);
        }
    }
    if (isset ($sorted_hooks [$hook])) {
        unset ($sorted_hooks [$hook]);
    }
    return true;
}

/**
 * 触发HOOK
 *
 * @global array $rtk_hooks 系统所有HOOK的回调
 * @global array $sorted_hooks 当前的HOOK回调是否已经排序
 * @global array $triggered_hooks 已经执行过的回调
 * @global array $triggering_hooks 正在执行的回调
 * @param string $hook HOOK名称
 * @param mixed $arg 参数
 * @return string 如果HOOK的回调中有输出,则返回输出
 */
function fire($hook, $arg = "") {
    global $rtk_hooks, $sorted_hooks, $triggered_hooks, $triggering_hooks;

    if (is_array($triggered_hooks)) {
        $triggered_hooks [] = $hook;
    } else {
        $triggered_hooks = array($hook);
    }
    $triggering_hooks [] = $hook;
    // Do 'all' actions first
    if (isset ($rtk_hooks ['all'])) {
        $all_args = func_get_args();
        __rt_call_all_hook($all_args);
    }
    if (!isset ($rtk_hooks [$hook])) { //没有该HOOK的回调
        array_pop($triggering_hooks);
        return;
    }
    $args = array();
    if (is_array($arg) && 1 == count($arg) && is_object($arg [0])) { // array(&$this)
        $args [] = & $arg [0];
    } else {
        $args [] = $arg;
    }
    for ($a = 2; $a < func_num_args(); $a++) {
        $args [] = func_get_arg($a);
    }

    //对hook的回调进行排序
    if (!isset ($sorted_hooks [$hook])) {
        ksort($rtk_hooks [$hook]);
        $sorted_hooks [$hook] = true;
    }
    //重置hook回调数组
    reset($rtk_hooks [$hook]);

    do {
        foreach (( array )current($rtk_hooks [$hook]) as $the_) {
            if (!is_null($the_ ['func'])) {
                call_user_func_array($the_ ['func'], array_slice($args, 0, ( int )$the_ ['accepted_args']));
            }
        }
    } while (next($rtk_hooks [$hook]) !== false);
    array_pop($triggering_hooks);
}

/**
 * 参数以数组的方式传送
 *
 * @global array $rtk_hooks 系统所有HOOK的回调
 * @global array $sorted_hooks 当前的HOOK回调是否已经排序
 * @global array $triggered_hooks 已经执行过的回调
 * @global array $triggering_hooks 正在执行的回调
 * @see fire
 * @param string $hook HOOK
 * @param array $args 参数
 */
function fire_ref_array($hook, $args) {
    global $rtk_hooks, $sorted_hooks, $triggered_hooks, $triggering_hooks;

    if (is_array($triggered_hooks)) {
        $triggered_hooks [] = $hook;
    } else {
        $triggered_hooks = array($hook);
    }
    $triggering_hooks [] = $hook;
    // Do 'all' actions first
    if (isset ($rtk_hooks ['all'])) {
        $all_args = func_get_args();
        __rt_call_all_hook($all_args);
    }
    if (!isset ($rtk_hooks [$hook])) { //没有该HOOK的回调
        array_pop($triggering_hooks);
        return;
    }
    //对hook的回调进行排序
    if (!isset ($sorted_hooks [$hook])) {
        ksort($rtk_hooks [$hook]);
        $sorted_hooks [$hook] = true;
    }
    //重置hook回调数组
    reset($rtk_hooks [$hook]);
    do {
        foreach (( array )current($rtk_hooks [$hook]) as $the_) {
            if (!is_null($the_ ['func'])) {
                call_user_func_array($the_ ['func'], array_slice($args, 0, ( int )$the_ ['accepted_args']));
            }
        }
    } while (next($rtk_hooks [$hook]) !== false);
    array_pop($triggering_hooks);
}

/**
 * 调用与指定过滤器关联的HOOK
 *
 *
 * @global array $rtk_hooks 系统所有HOOK的回调
 * @global array $sorted_hooks 当前的HOOK回调是否已经排序
 * @global array $triggering_hooks 正在执行的回调
 * @param string $filter 过滤器名
 * @param mixed $value
 * @param mixed $var....
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filter($filter, $value) {
    global $rtk_hooks, $sorted_hooks, $triggering_hooks;

    $args = array();
    $triggering_hooks [] = $filter;

    if (isset ($rtk_hooks ['all'])) {
        $args = func_get_args();
        __rt_call_all_hook($args);
    }

    if (!isset ($rtk_hooks [$filter])) {
        array_pop($triggering_hooks);
        return $value;
    }

    if (!isset ($sorted_hooks [$filter])) {
        ksort($rtk_hooks [$filter]);
        $sorted_hooks [$filter] = true;
    }

    reset($rtk_hooks [$filter]);

    if (empty ($args)) {
        $args = func_get_args();
    }

    do {
        foreach (( array )current($rtk_hooks [$filter]) as $the_) {
            if (!is_null($the_ ['func'])) {
                $args [1] = $value;
                $value = call_user_func_array($the_ ['func'], array_slice($args, 1, ( int )$the_ ['accepted_args']));
            }
        }
    } while (next($rtk_hooks [$filter]) !== false);

    array_pop($triggering_hooks);

    return $value;
}

/**
 * 正在触发的HOOK(包括Filter)
 *
 * @global array
 * @return string the hook name whitch  is triggering
 */
function triggering_hook() {
    global $triggering_hooks;
    return end($triggering_hooks);
}

/**
 * $hook 被触发了多少次
 *
 * @global array
 * @param string $hook hook 名称
 * @return int
 */
function triggered_hook($hook) {
    global $triggered_hooks;

    if (empty ($triggered_hooks)) {
        return 0;
    }
    return count(array_keys($triggered_hooks, $hook));
}

/**
 * Check if any hook has been registered.
 *
 * @global array $rtk_hooks Stores all of the hooks
 * @see wordpress has_filter
 * @param string $tag The name of the filter hook.
 * @param callback $function_to_check optional.  If specified, return the priority of that function on this hook or false if not attached.
 * @return int|boolean Optionally returns the priority on that hook for the specified function.
 */
function has_hook($hook, $function_to_check = false) {
    global $rtk_hooks;

    $has = !empty ($rtk_hooks [$hook]);
    if (false === $function_to_check || false == $has) {
        return $has;
    }
    if (!$idx = __rt_hook_unique_id($hook, $function_to_check, false)) {
        return false;
    }
    foreach (( array )array_keys($rtk_hooks [$hook]) as $priority) {
        if (isset ($rtk_hooks [$hook] [$priority] [$idx])) {
            return $priority;
        }
    }
    return false;
}

/**
 * 调用 all HOOK 回调
 *
 * @global array
 * @param array $args 参数
 */
function __rt_call_all_hook($args) {
    global $rtk_hooks;

    reset($rtk_hooks ['all']);
    do {
        foreach (( array )current($rtk_hooks ['all']) as $the_) {
            if (!is_null($the_ ['func'])) {
                call_user_func_array($the_ ['func'], $args);
            }
        }
    } while (next($rtk_hooks ['all']) !== false);
}

/**
 * Build Unique ID for storage and retrieval.
 *
 * @see wordpress
 * @global array $rtk_hooks Storage for all of the filters and actions
 * @staticvar $filter_id_count
 * @param string $hook_name
 * @param string $function
 * @param string $priority
 * @return string|bool Unique ID for usage as array key or false if $priority === false and $function is an object reference, and it does not already have a uniqe id.
 */
function __rt_hook_unique_id($hook_name, $function, $priority) {
    global $rtk_hooks;
    static $filter_id_count = 0;

    if (is_string($function)) {
        return $function;
    } else if (is_object($function [0])) {
        // Object Class Calling
        if (function_exists('spl_object_hash')) {
            return spl_object_hash($function [0]) . $function [1];
        } else {
            $obj_idx = get_class($function [0]) . $function [1];
            if (!isset ($function [0]->wp_filter_id)) {
                if (false === $priority) {
                    return false;
                }
                $obj_idx .= isset ($rtk_hooks [$hook_name] [$priority]) ? count(( array )$rtk_hooks [$hook_name] [$priority]) : $filter_id_count;
                $function [0]->wp_filter_id = $filter_id_count;
                ++$filter_id_count;
            } else {
                $obj_idx .= $function [0]->wp_filter_id;
            }

            return $obj_idx;
        }
    } else if (is_string($function [0])) {
        // Static Calling
        return $function [0] . $function [1];
    }
}