--TEST--
Test session_start() function : variation
--SKIPIF--
<?php include('skipif.inc'); ?>
--FILE--
<?php

ob_start();

/* 
 * Prototype : bool session_start(void)
 * Description : Initialize session data
 * Source code : ext/session/session.c 
 */

echo "*** Testing session_start() : variation ***\n";

var_dump(session_start());
var_dump(session_write_close());
var_dump(session_start());
var_dump(session_write_close());
var_dump(session_start());
var_dump(session_write_close());
var_dump(session_start());
var_dump(session_write_close());
var_dump(session_start());
var_dump(session_write_close());
var_dump(session_destroy(-1));

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_start() : variation ***
bool(true)
NULL
bool(true)
NULL
bool(true)
NULL
bool(true)
NULL
bool(true)
NULL

Notice: session_destroy(): Immediate session data removal may cause random lost sessions. It is advised to set few secounds duration at least on stable network, few miniutes for unstable network in %s on line %d

Warning: session_destroy(): Trying to destroy uninitialized session in %s on line %d
bool(false)
Done

