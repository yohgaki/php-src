/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2016 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Derick Rethans <derick@php.net>                             |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifndef FILTER_PRIVATE_H
#define FILTER_PRIVATE_H

/* Filter flags - some flags are reusing value */
#define FILTER_FLAG_NONE                    0x0000

#define FILTER_REQUIRE_ARRAY             0x1000000
#define FILTER_REQUIRE_SCALAR            0x2000000

#define FILTER_FORCE_ARRAY               0x4000000
#define FILTER_NULL_ON_FAILURE           0x8000000

#define FILTER_FLAG_ALLOW_OCTAL             0x0001
#define FILTER_FLAG_ALLOW_HEX               0x0002

#define FILTER_FLAG_STRIP_LOW               0x0004
#define FILTER_FLAG_STRIP_HIGH              0x0008
#define FILTER_FLAG_ENCODE_LOW              0x0010
#define FILTER_FLAG_ENCODE_HIGH             0x0020
#define FILTER_FLAG_ENCODE_AMP              0x0040
#define FILTER_FLAG_NO_ENCODE_QUOTES        0x0080
#define FILTER_FLAG_EMPTY_STRING_NULL       0x0100
#define FILTER_FLAG_STRIP_BACKTICK          0x0200

#define FILTER_FLAG_ALLOW_FRACTION          0x1000
#define FILTER_FLAG_ALLOW_THOUSAND          0x2000
#define FILTER_FLAG_ALLOW_SCIENTIFIC        0x4000

#define FILTER_FLAG_SCHEME_REQUIRED        0x010000
#define FILTER_FLAG_HOST_REQUIRED          0x020000
#define FILTER_FLAG_PATH_REQUIRED          0x040000
#define FILTER_FLAG_QUERY_REQUIRED         0x080000

#define FILTER_FLAG_IPV4                   0x100000
#define FILTER_FLAG_IPV6                   0x200000
#define FILTER_FLAG_NO_RES_RANGE           0x400000
#define FILTER_FLAG_NO_PRIV_RANGE          0x800000

#define FILTER_FLAG_HOSTNAME               0x100000

#define FILTER_FLAG_EMAIL_UNICODE          0x100000

/* FILTER_VALIDATE_STRING encoding options */
#define FILTER_STRING_ENCODING_PASS               0
#define FILTER_STRING_ENCODING_UTF8               1

#define FILTER_FLAG_STRING_RAW               0x0001
#define FILTER_FLAG_STRING_ALLOW_CNTRL       0x0002
#define FILTER_FLAG_STRING_ALLOW_TAB         0x0004
#define FILTER_FLAG_STRING_ALLOW_NEWLINE     0x0008
#define FILTER_FLAG_STRING_ALPHA             0x0010
#define FILTER_FLAG_STRING_NUM               0x0020
#define FILTER_FLAG_STRING_ALNUM             0x0040
#define FILTER_FLAG_BOOL_ALLOW_EMPTY         0x0080

/* filter_require_*_array() function behavior options */
#define FILTER_OPTS_ADD_EMPTY                0x0001
#define FILTER_OPTS_DISABLE_EXCEPTION        0x0002
#define FILTER_OPTS_RAISE_ERROR              0x0004

/* Filters */
#define FILTER_VALIDATE_DEFAULT       0x0104

#define FILTER_VALIDATE_INT           0x0101
#define FILTER_VALIDATE_BOOLEAN       0x0102
#define FILTER_VALIDATE_FLOAT         0x0103
#define FILTER_VALIDATE_STRING        0x0104

#define FILTER_VALIDATE_REGEXP        0x0110
#define FILTER_VALIDATE_URL           0x0111
#define FILTER_VALIDATE_EMAIL         0x0112
#define FILTER_VALIDATE_IP            0x0113
#define FILTER_VALIDATE_MAC           0x0114
#define FILTER_VALIDATE_DOMAIN        0x0115
#define FILTER_VALIDATE_LAST          0x0115

#define FILTER_VALIDATE_ALL           0x0100

/*
 * FIXME: Falling back to filter that does nothing as defualt
 * is not good for security feature.
 */
#define FILTER_DEFAULT                0x0204
#define FILTER_UNSAFE_RAW             0x0204

#define FILTER_SANITIZE_STRING        0x0201
#define FILTER_SANITIZE_ENCODED       0x0202
#define FILTER_SANITIZE_SPECIAL_CHARS 0x0203
#define FILTER_SANITIZE_EMAIL         0x0205
#define FILTER_SANITIZE_URL           0x0206
#define FILTER_SANITIZE_NUMBER_INT    0x0207
#define FILTER_SANITIZE_NUMBER_FLOAT  0x0208
#define FILTER_SANITIZE_MAGIC_QUOTES  0x0209
#define FILTER_SANITIZE_FULL_SPECIAL_CHARS 0x020a
#define FILTER_SANITIZE_LAST          0x020a

#define FILTER_SANITIZE_ALL           0x0200

#define FILTER_CALLBACK               0x0400


extern zend_class_entry *php_filter_validate_exception_class_entry;
void php_filter_throw_validate_exception(zval *invalid_key, zval *invalid_value, zend_long filter_id, zend_long filter_options, char *format, ...);


#define PHP_FILTER_EXCEPTION(func_opts) (!(func_opts & FILTER_OPTS_DISABLE_EXCEPTION))
#define PHP_FILTER_ERROR(func_opts) (func_opts & FILTER_OPTS_RAISE_ERROR)
#define PHP_FILTER_ADD_EMPTY(func_opts) (func_opts & FILTER_OPTS_ADD_EMPTY)

#define PHP_FILTER_ID_EXISTS(id) \
((id >= FILTER_SANITIZE_ALL && id <= FILTER_SANITIZE_LAST) \
|| (id >= FILTER_VALIDATE_ALL && id <= FILTER_VALIDATE_LAST) \
|| id == FILTER_CALLBACK)

#define PHP_FILTER_SAVE_INVALID_KEY() \
	if (!Z_ISUNDEF(IF_G(invalid_key))) { \
		zval_ptr_dtor(&IF_G(invalid_key)); \
	} \
	ZVAL_COPY(&IF_G(invalid_key), &IF_G(current_key))

#define PHP_FILTER_RAISE_EXCEPTION(message, ...) \
	PHP_FILTER_SAVE_INVALID_KEY(); \
	do { \
		if (IF_G(raise_exception)) { \
			/* zend_throw_exception_ex(spl_ce_UnexpectedValueException, 0, message, ##__VA_ARGS__); */ \
			php_filter_throw_validate_exception(&IF_G(invalid_key), value, filter_id, flags, message, ##__VA_ARGS__); \
		} \
	} while(0)

#endif /* FILTER_PRIVATE_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
