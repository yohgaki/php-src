--TEST--
Bug #69111 Crash in SessionHandler::read()
--SKIPIF--
--XFAIL--
It is still a leak
<?php include('skipif.inc'); ?>
--FILE--
<?php
$sh = new SessionHandler;
session_set_save_handler($sh);

$savePath = ini_get('session.save_path');
$sessionName = ini_get('session.name');

// session_start(); // Uncommenting this makes it not crash when reading the session (see below), but it will not return any data. 

$sh->open($savePath, $sessionName);
$sh->write("foo", "bar");
var_dump($sh->read(@$id));
?>
--EXPECTF--
Warning: SessionHandler::open(): Session is not active in %s on line %d

Warning: SessionHandler::write(): Session is not active in %s on line %d

Warning: SessionHandler::read(): Session is not active in %s on line %d
bool(false)
