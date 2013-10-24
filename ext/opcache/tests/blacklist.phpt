--TEST--
Blacklist (with glob, quote and comments)
--INI--
opcache.enable=1
opcache.enable_cli=1
opcache.blacklist_filename={PWD}/opcache-*.blacklist
--SKIPIF--
<?php require_once('skipif.inc'); ?>
--FILE--
<?php
$conf = opcache_get_configuration();
$conf = $conf['blacklist'];
$conf[3] = preg_replace("!^\\Q".dirname(__FILE__)."\\E!", "__DIR__", $conf[3]); 
$conf[4] = preg_replace("!^\\Q".dirname(__FILE__)."\\E!", "__DIR__", $conf[4]); 
print_r($conf);
include("blacklist.inc");
$status = opcache_get_status();
print_r(count($status['scripts']));
?>
--EXPECT--
Array
(
    [0] => /path/to/foo
    [1] => /path/to/foo2
    [2] => /path/to/bar
    [3] => __DIR__/blacklist.inc
    [4] => __DIR__/current.php
    [5] => /tmp/path/?nocache.inc
    [6] => /tmp/path/*/somedir
)
ok
1
