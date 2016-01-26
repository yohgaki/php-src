--TEST--
Bug #70219 Use after free vulnerability in session deserializer
--SKIPIF--
<?php
if (!extension_loaded("session")) {
    die("skip Session module not loaded");
}
?>
--FILE--
<?php
ini_set('session.serialize_handler', 'php_serialize');
session_start();

class obj implements Serializable {
    var $data;
    function serialize() {
        return serialize($this->data);
    }
    function unserialize($data) {
        session_decode($data);
    }
}

$inner = 'r:2;';
$exploit = 'a:2:{i:0;C:3:"obj":'.strlen($inner).':{'.$inner.'}i:1;C:3:"obj":'.strlen($inner).':{'.$inner.'}}';

$data = unserialize($exploit);

for ($i = 0; $i < 5; $i++) {
    $v[$i] = 'hi'.$i;
}

var_dump($data);
var_dump($_SESSION);
?>
--EXPECTF--
Warning: session_decode(): Malformed session data detected: files (path: ) Session aborted in %s on line 11

Warning: session_decode(): Session is not active. You cannot decode session data in %s on line 11
array(2) {
  [0]=>
  object(obj)#1 (1) {
    ["data"]=>
    NULL
  }
  [1]=>
  object(obj)#2 (1) {
    ["data"]=>
    NULL
  }
}
array(0) {
}
