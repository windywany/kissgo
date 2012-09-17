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
if (version_compare(phpversion(), '5.4', '<')) {
    interface SessionHandlerInterface {
        function close();

        function destroy($session_id);

        function gc($max_life_time);

        function open($save_path, $name);

        function read($session_id);

        function write($session_id, $session_data);
    }

    $__ksg_session_handler = apply_filter('get_session_handler', null);
    if ($__ksg_session_handler instanceof SessionHandlerInterface) {
        session_set_save_handler(
            array($__ksg_session_handler, 'open'),
            array($__ksg_session_handler, 'close'),
            array($__ksg_session_handler, 'read'),
            array($__ksg_session_handler, 'write'),
            array($__ksg_session_handler, 'destroy'),
            array($__ksg_session_handler, 'gc')
        );
        register_shutdown_function('session_write_close');
    }
} else {
    $__ksg_session_handler = apply_filter('get_session_handler', null);
    if ($__ksg_session_handler instanceof SessionHandlerInterface) {
        @session_set_save_handler($__ksg_session_hander, true);
    }
}
// TODO 调用 session_start()之前的一些处理工作
# session_set_cookie_params()
// END OF FILE session.php