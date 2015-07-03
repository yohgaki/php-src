--TEST--
Bug #69969 (Poor cleaning of user session handlers)
--SKIPIF--
<?php include('skipif.inc'); ?>
--INI--
session.save_handler=files
--FILE--
<?php
ob_start('initStaticVar');

function open($savePath, $sessionName) {
    return true;
}

function read() {
    return '';
}

function write($id) {
    return true;
}

function destroy($id) {
    return true;
}

function gc($maxlifetime) {
    return true;
}

function close() {
    return true;
}

function create_id() {
	echo 'This is id()';
	return '123456789';
}

function initStaticVar($content) {
    static $var = null;
    $var = new SessionHandlerSpoofer;
    return  'foo';
}


class SessionHandlerSpoofer
{
    function __destruct() {
        $res = session_set_save_handler(
			'open',
			'read',
			'write',
			'destroy',
			'gc',
			'close',
			'create_id'
        );
    }
}

session_start();
session_commit();

session_set_save_handler(
    'open',
    'read',
    'write',
    'destroy',
    'gc',
    'close'
);

session_start();
session_commit();
?>
--EXPECTF--

