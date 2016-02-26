--TEST--
Bug #32330 (session_destroy, "Failed to initialize storage module", custom session handler)
--SKIPIF--
<?php include('skipif.inc'); ?>
--INI--
session.use_trans_sid=0
session.use_cookies=1
session.name=sid
session.save_path=/tmp
session.gc_probability=1
session.gc_divisor=1
--FILE--
<?php
error_reporting(E_ALL);

function sOpen($path, $name)
{
	echo "open: path = {$path}, name = {$name}\n";
	return TRUE;
}

function sClose()
{
	echo "close\n";
	return TRUE;
}

function sRead($id)
{
	echo "read: id = {$id}\n";
	return '';
}

function sWrite($id, $data)
{
	echo "write: id = {$id}, data = {$data}\n";
	return TRUE;
}

function sDestroy($id)
{
	echo "destroy: id = {$id}\n";
	return TRUE;
}

function sGC($maxlifetime)
{
	echo "gc: maxlifetime = {$maxlifetime}\n";
	return TRUE;
}

session_set_save_handler( 'sOpen', 'sClose', 'sRead', 'sWrite', 'sDestroy', 'sGC' );

// without output buffering, the debug messages will cause all manner of warnings
ob_start();

session_start();
$_SESSION['A'] = 'B';
session_write_close();

session_start();
$_SESSION['C'] = 'D';
session_destroy(true);

session_start();
$_SESSION['E'] = 'F';
// Don't try to destroy this time!

?>
--EXPECTF--
open: path = /tmp, name = sid
gc: maxlifetime = %d
read: id = %s
write: id = %s, data = A|s:1:"B";__PHP_SESSION__|a:3:{s:7:"CREATED";i:%d;s:7:"UPDATED";i:%d;s:4:"SIDS";a:0:{}}
close
open: path = /tmp, name = sid
gc: maxlifetime = %d
read: id = %s
destroy: id = %s
close
open: path = /tmp, name = sid
gc: maxlifetime = %d
read: id = %s
write: id = %s, data = E|s:1:"F";__PHP_SESSION__|a:3:{s:7:"CREATED";i:%d;s:7:"UPDATED";i:%d;s:4:"SIDS";a:0:{}}
close
