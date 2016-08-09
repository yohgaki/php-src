--TEST--
filter_require_var() and string validation rules
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--FILE--
<?php
$strings = array(
	'empty' => '',
	'num' =>'123456789',
	'alpha' => 'abcXYZ',
	'almum' => 'abc1234',
	'mixed' => 'abcXYZ! "#$%&()',
	'multiline' => "abc\nXYZ\n",
	'cntrl' => "\b\0abc",
	'utf8' => '日本',
	'urf8broken' => "\xF0\xF0日本",
);
$flags = array(
	'none' => 0,
	'raw' => FILTER_FLAG_STRING_RAW,
	'allow_cntrl' => FILTER_FLAG_STRING_ALLOW_CNTRL,
	'allow_newline' => FILTER_FLAG_STRING_ALLOW_NEWLINE,
	'alpha' => FILTER_FLAG_STRING_ALPHA,
	'num' => FILTER_FLAG_STRING_NUM,
	'alnum' => FILTER_FLAG_STRING_ALNUM,
);

echo "***Without options***\n";
foreach($strings as $type => $val) {
	foreach($flags as $flag => $fval) {
		try {
			var_dump(filter_require_var($val, FILTER_VALIDATE_STRING, array('flags'=>$flag)));
		} catch (Exception $e) {
			var_dump('INPUT('.$type.'): '.$val, 'FLAG:'.$flag, 'ErrorMsg: '.$e->getMessage());
		}
	}
}

$options = array(
	'min_bytes' => 0,
	'max_bytes' => 10,
	'encoding' => FILTER_STRING_ENCODING_PASS,
);
echo "***With options***\n";
foreach($strings as $type => $val) {
	foreach($flags as $flag => $fval) {
		try {
			var_dump(filter_require_var($val, FILTER_VALIDATE_STRING, array('flags'=>$flag, 'options'=>$options)));
		} catch (Exception $e) {
			var_dump('INPUT('.$type.'): '.$val, 'FLAG:'.$flag, 'ErrorMsg: '.$e->getMessage());
		}
	}
}

--EXPECT--
***Without options***
string(14) "INPUT(empty): "
string(9) "FLAG:none"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(14) "INPUT(empty): "
string(8) "FLAG:raw"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(14) "INPUT(empty): "
string(16) "FLAG:allow_cntrl"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(14) "INPUT(empty): "
string(18) "FLAG:allow_newline"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(14) "INPUT(empty): "
string(10) "FLAG:alpha"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(14) "INPUT(empty): "
string(8) "FLAG:num"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(14) "INPUT(empty): "
string(10) "FLAG:alnum"
string(111) "ErrorMsg: String validation: Too short (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(15) "abcXYZ! "#$%&()"
string(15) "abcXYZ! "#$%&()"
string(15) "abcXYZ! "#$%&()"
string(15) "abcXYZ! "#$%&()"
string(15) "abcXYZ! "#$%&()"
string(15) "abcXYZ! "#$%&()"
string(15) "abcXYZ! "#$%&()"
string(26) "INPUT(multiline): abc
XYZ
"
string(9) "FLAG:none"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:raw"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(16) "FLAG:allow_cntrl"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(18) "FLAG:allow_newline"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alpha"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:num"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alnum"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(9) "FLAG:none"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:raw"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(16) "FLAG:allow_cntrl"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(18) "FLAG:allow_newline"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alpha"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:num"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alnum"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(27) "INPUT(urf8broken): ��日本"
string(9) "FLAG:none"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(27) "INPUT(urf8broken): ��日本"
string(8) "FLAG:raw"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(27) "INPUT(urf8broken): ��日本"
string(16) "FLAG:allow_cntrl"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(27) "INPUT(urf8broken): ��日本"
string(18) "FLAG:allow_newline"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alpha"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(27) "INPUT(urf8broken): ��日本"
string(8) "FLAG:num"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alnum"
string(124) "ErrorMsg: String validation: Invalid UTF-8 encoding (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
***With options***
string(0) ""
string(0) ""
string(0) ""
string(0) ""
string(0) ""
string(0) ""
string(0) ""
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(9) "123456789"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(6) "abcXYZ"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(7) "abc1234"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(9) "FLAG:none"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(8) "FLAG:raw"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(16) "FLAG:allow_cntrl"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(18) "FLAG:allow_newline"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alpha"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(8) "FLAG:num"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alnum"
string(110) "ErrorMsg: String validation: Too long (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(9) "FLAG:none"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:raw"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(16) "FLAG:allow_cntrl"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(18) "FLAG:allow_newline"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alpha"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:num"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alnum"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(9) "FLAG:none"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:raw"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(16) "FLAG:allow_cntrl"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(18) "FLAG:allow_newline"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alpha"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:num"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alnum"
string(123) "ErrorMsg: String validation: Control char detected (invalid_key: N/A, filter_name: validate_string, filter_flags: 33554432)"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(8) "��日本"
string(8) "��日本"
string(8) "��日本"
string(8) "��日本"
string(8) "��日本"
string(8) "��日本"
string(8) "��日本"
