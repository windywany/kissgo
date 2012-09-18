<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-16 下午6:16
 * $Id$
 */
/**
 * Session处理器接口
 *
 */
interface SessionHandlerInterface {
    function close();

    function destroy($session_id);

    function gc($max_life_time);

    function open($save_path, $name);

    function read($session_id);

    function write($session_id, $session_data);
}

// use cookie for session id
@ini_set('session.use_cookies', 1);
