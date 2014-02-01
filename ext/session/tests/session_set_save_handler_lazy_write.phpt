--TEST--
Test session_set_save_handler() function : test lazy_write
--INI--
session.save_path=
session.name=PHPSESSID
--SKIPIF--
<?php include('skipif.inc'); ?>
--FILE--
<?php

ob_start();

/*
 * Prototype : bool session_set_save_handler(callback $open, callback $close, callback $read, callback $write, callback $destroy, callback $gc)
 * Description : Sets user-level session storage functions
 * Source code : ext/session/session.c
 */

echo "*** Testing session_set_save_handler() : test lazy_write ***\n";

require_once "save_handler.inc";
$path = dirname(__FILE__);
session_save_path($path);
session_set_save_handler("open", "close", "read", "write", "destroy", "gc");

session_start(['lazy_write'=>true]);
$session_id = session_id();
$_SESSION["Blah"] = "Hello World!";
$_SESSION["Foo"] = FALSE;
$_SESSION["Guff"] = 1234567890;
var_dump($_SESSION);

session_write_close();
session_unset();
var_dump($_SESSION);

echo "Starting session again..!\n";
session_id($session_id);
session_set_save_handler("open", "close", "read", "write", "destroy", "gc");
session_start(['lazy_write'=>true]);
var_dump($_SESSION);
$_SESSION['Bar'] = 'Foo';
session_write_close();

echo "Starting session again..!\n";
session_id($session_id);
session_set_save_handler("open", "close", "read", "write", "destroy", "gc");
session_start(['lazy_write'=>true]);
var_dump($_SESSION);
// $_SESSION should be the same, but no "update" should call write()
session_write_close();

// Cleanup
session_id($session_id);
session_start();
session_destroy(TRUE);

ob_end_flush();
?>
--EXPECTF--
*** Testing session_set_save_handler() : test lazy_write ***

Open [%s,PHPSESSID]
Read [%s,%s]
array(3) {
  ["Blah"]=>
  string(12) "Hello World!"
  ["Foo"]=>
  bool(false)
  ["Guff"]=>
  int(1234567890)
}
Write [%s,%s,Blah|s:12:"Hello World!";Foo|b:0;Guff|i:1234567890;]
Close [%s,PHPSESSID]
array(3) {
  ["Blah"]=>
  string(12) "Hello World!"
  ["Foo"]=>
  bool(false)
  ["Guff"]=>
  int(1234567890)
}
Starting session again..!
Open [%s,PHPSESSID]
Read [%s,%s]
array(3) {
  ["Blah"]=>
  string(12) "Hello World!"
  ["Foo"]=>
  bool(false)
  ["Guff"]=>
  int(1234567890)
}
Write [%s,%s,Blah|s:12:"Hello World!";Foo|b:0;Guff|i:1234567890;Bar|s:3:"Foo";]
Close [%s,PHPSESSID]
Starting session again..!
Open [%s,PHPSESSID]
Read [%s,%s]
array(4) {
  ["Blah"]=>
  string(12) "Hello World!"
  ["Foo"]=>
  bool(false)
  ["Guff"]=>
  int(1234567890)
  ["Bar"]=>
  string(3) "Foo"
}
Write [%s,%s,Blah|s:12:"Hello World!";Foo|b:0;Guff|i:1234567890;Bar|s:3:"Foo";]
Close [%s,PHPSESSID]
Open [%s,PHPSESSID]
Read [%s,%s]
Destroy [%s,%s]
Close [%s,PHPSESSID]