--TEST--
Test session_name() function : variation
--INI--
session.save_path=
session.name=PHPSESSID
--SKIPIF--
<?php include('skipif.inc'); ?>
--FILE--
<?php
function error_handler($errno, $errstr, $errfile, $errline)
{
    if ($errno & E_NOTICE) {
        return ture; // Ignore notices
    }
    if ($errno & E_RECOVERABLE_ERROR) {
        // Handle E_REVOCERABLE_ERROR
        echo "\nE_RECOVERABLE_ERROR: {$errstr} in {$errfile} on line {$errline}\n";
        return true; // Continue execution
    }
    return false;
}

set_error_handler('error_handler');

ob_start();

/* 
 * Prototype : string session_name([string $name])
 * Description : Get and/or set the current session name
 * Source code : ext/session/session.c 
 */

echo "*** Testing session_name() : variation ***\n";

var_dump(session_name("\0"));
var_dump(session_start());
var_dump(session_name());
var_dump(session_destroy());
var_dump(session_name());

var_dump(session_name("\t"));
var_dump(session_start());
var_dump(session_name());
var_dump(session_destroy());
var_dump(session_name());

var_dump(session_name(""));
var_dump(session_start());
var_dump(session_name());
var_dump(session_destroy());
var_dump(session_name());

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_name() : variation ***
string(9) "PHPSESSID"
bool(true)
string(9) "PHPSESSID"
bool(true)
string(9) "PHPSESSID"
string(9) "PHPSESSID"
bool(true)
string(1) "	"
bool(true)
string(1) "	"

Warning: session_name(): session.name cannot be a numeric or empty '' in %s on line %d
string(1) "	"
bool(true)
string(1) "	"
bool(true)
string(1) "	"
Done

