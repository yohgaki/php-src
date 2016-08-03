--TEST--
validate_var() and string validation rules
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
	'multi_line' => FILTER_FLAG_STRING_MULTI_LINE,
	'alpha' => FILTER_FLAG_STRING_ALPHA,
	'num' => FILTER_FLAG_STRING_NUM,
	'alnum' => FILTER_FLAG_STRING_ALNUM,
);
$option = FILTER_STRING_ENCODING_UTF8;

echo "***Without options***\n";
foreach($strings as $type => $val) {
	foreach($flags as $flag => $fval) {
		try {
			var_dump(validate_var($val, FILTER_VALIDATE_STRING, array('flags'=>$flag)));
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
			var_dump(validate_var($val, FILTER_VALIDATE_STRING, array('flags'=>$flag, 'options'=>$options)));
		} catch (Exception $e) {
			var_dump('INPUT('.$type.'): '.$val, 'FLAG:'.$flag, 'ErrorMsg: '.$e->getMessage());
		}
	}
}

--EXPECT--
***Without options***
string(14) "INPUT(empty): "
string(9) "FLAG:none"
string(38) "ErrorMsg: String validation: Too short"
string(14) "INPUT(empty): "
string(8) "FLAG:raw"
string(38) "ErrorMsg: String validation: Too short"
string(14) "INPUT(empty): "
string(16) "FLAG:allow_cntrl"
string(38) "ErrorMsg: String validation: Too short"
string(14) "INPUT(empty): "
string(15) "FLAG:multi_line"
string(38) "ErrorMsg: String validation: Too short"
string(14) "INPUT(empty): "
string(10) "FLAG:alpha"
string(38) "ErrorMsg: String validation: Too short"
string(14) "INPUT(empty): "
string(8) "FLAG:num"
string(38) "ErrorMsg: String validation: Too short"
string(14) "INPUT(empty): "
string(10) "FLAG:alnum"
string(38) "ErrorMsg: String validation: Too short"
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
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:raw"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(16) "FLAG:allow_cntrl"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(15) "FLAG:multi_line"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alpha"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:num"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alnum"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(9) "FLAG:none"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:raw"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(16) "FLAG:allow_cntrl"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(15) "FLAG:multi_line"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alpha"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:num"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alnum"
string(50) "ErrorMsg: String validation: Control char detected"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(6) "日本"
string(27) "INPUT(urf8broken): ��日本"
string(9) "FLAG:none"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
string(27) "INPUT(urf8broken): ��日本"
string(8) "FLAG:raw"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
string(27) "INPUT(urf8broken): ��日本"
string(16) "FLAG:allow_cntrl"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
string(27) "INPUT(urf8broken): ��日本"
string(15) "FLAG:multi_line"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alpha"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
string(27) "INPUT(urf8broken): ��日本"
string(8) "FLAG:num"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alnum"
string(51) "ErrorMsg: String validation: Invalid UTF-8 encoding"
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
string(37) "ErrorMsg: String validation: Too long"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(8) "FLAG:raw"
string(37) "ErrorMsg: String validation: Too long"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(16) "FLAG:allow_cntrl"
string(37) "ErrorMsg: String validation: Too long"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(15) "FLAG:multi_line"
string(37) "ErrorMsg: String validation: Too long"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alpha"
string(37) "ErrorMsg: String validation: Too long"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(8) "FLAG:num"
string(37) "ErrorMsg: String validation: Too long"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alnum"
string(37) "ErrorMsg: String validation: Too long"
string(26) "INPUT(multiline): abc
XYZ
"
string(9) "FLAG:none"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:raw"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(16) "FLAG:allow_cntrl"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(15) "FLAG:multi_line"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alpha"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:num"
string(50) "ErrorMsg: String validation: Control char detected"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alnum"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(9) "FLAG:none"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:raw"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(16) "FLAG:allow_cntrl"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(15) "FLAG:multi_line"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alpha"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:num"
string(50) "ErrorMsg: String validation: Control char detected"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alnum"
string(50) "ErrorMsg: String validation: Control char detected"
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