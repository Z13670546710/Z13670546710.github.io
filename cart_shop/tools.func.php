<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2019/1/23
 * Time: 20:31
 */

function setSession($key, $data, $prefix = '')
{
    session_id() || @session_start();
    if (!empty($prefix)) {
        $_SESSION[$prefix][$key] = $data;
    } else {
        $_SESSION[$key] = $data;
    }
}

function getSession($key, $prefix = '')
{
    session_id() || @session_start();
    if (!empty($prefix)) {
        return isset($_SESSION[$prefix][$key]) ? $_SESSION[$prefix][$key] : [];
    } else {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : [];
    }
}

function deleteSession($key, $prefix = '')
{
    session_id() || @session_start();
    if (!empty($prefix)) {
        $_SESSION[$prefix][$key] = null;
    } else {
        $_SESSION[$key] = null;
    }
}

function setInfo($info)
{
    setSession('info', $info, 'system');
}

function getInfo()
{
   $info = getSession('info', 'system');
   deleteSession('info', 'system');
   return $info;
}

function hasInfo()
{
   return !empty(getSession('info', 'system'));
}