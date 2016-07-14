/* Generated by re2c 0.14.3 */
#line 1 "ext/standard/url_scanner_ex.re"
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
  | Author: Sascha Schumann <sascha@schumann.cx>                         |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#include "php.h"

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#endif
#ifdef HAVE_LIMITS_H
#include <limits.h>
#endif

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "php_ini.h"
#include "php_globals.h"
#define STATE_TAG SOME_OTHER_STATE_TAG
#include "basic_functions.h"
#include "url.h"
#undef STATE_TAG

#define url_scanner url_scanner_ex

#include "zend_smart_str.h"

static void tag_dtor(zval *zv)
{
	free(Z_PTR_P(zv));
}

static PHP_INI_MH(OnUpdateTags)
{
	url_adapt_state_ex_t *ctx;
	char *key;
	char *tmp;
	char *lasts = NULL;

	ctx = &BG(url_adapt_state_ex);

	tmp = estrndup(ZSTR_VAL(new_value), ZSTR_LEN(new_value));

	if (ctx->tags)
		zend_hash_destroy(ctx->tags);
	else {
		ctx->tags = malloc(sizeof(HashTable));
		if (!ctx->tags) {
			return FAILURE;
		}
	}

	zend_hash_init(ctx->tags, 0, NULL, tag_dtor, 1);

	for (key = php_strtok_r(tmp, ",", &lasts);
			key;
			key = php_strtok_r(NULL, ",", &lasts)) {
		char *val;

		val = strchr(key, '=');
		if (val) {
			char *q;
			size_t keylen;

			*val++ = '\0';
			for (q = key; *q; q++)
				*q = tolower(*q);
			keylen = q - key;
			/* key is stored withOUT NUL
			   val is stored WITH    NUL */
			zend_hash_str_add_mem(ctx->tags, key, keylen, val, strlen(val)+1);
		}
	}

	efree(tmp);

	return SUCCESS;
}

PHP_INI_BEGIN()
	STD_PHP_INI_ENTRY("url_rewriter.tags", "a=href,area=href,frame=src,form=,fieldset=", PHP_INI_ALL, OnUpdateTags, url_adapt_state_ex, php_basic_globals, basic_globals)
PHP_INI_END()

#line 107 "ext/standard/url_scanner_ex.re"


#define YYFILL(n) goto done
#define YYCTYPE unsigned char
#define YYCURSOR p
#define YYLIMIT q
#define YYMARKER r

static inline void append_modified_url(smart_str *url, smart_str *dest, smart_str *url_app, const char *separator)
{
	php_url *url_parts;

	smart_str_0(url); /* FIXME: Bug #70480 php_url_prase_ex() crashes by processing chars exceed len */
	url_parts = php_url_parse_ex(ZSTR_VAL(url->s), ZSTR_LEN(url->s));

	if (!url_parts) {
		/* Ignore malformed URLs */
		smart_str_append_smart_str(dest, url);
		return;
	}

	if (url_parts->scheme ||
		(*(ZSTR_VAL(url->s)) == '/' && *(ZSTR_VAL(url->s)+1) == '/')) {
		/* Current URL scanner works only with relative local path */
		smart_str_append_smart_str(dest, url);
		php_url_free(url_parts);
		return;
	}

	/*
	 * When URL does not have path and query string add "/?".
	 * i.e. If URL is only "?foo=bar", should not add "/?".
	 */
	if (!url_parts->path && !url_parts->query) {
		/* URL is http://php.net or like */
		smart_str_append_smart_str(dest, url);
		smart_str_appendc(dest, '/');
		smart_str_appendc(dest, '?');
		smart_str_append_smart_str(dest, url_app);
		/* There should not be fragment. Just return */
		php_url_free(url_parts);
		return;
	}

	/* Schema/host/etc are handled for full path support in the future  */
	if (url_parts->scheme) {
		smart_str_appends(dest, url_parts->scheme);
	} else if (*(ZSTR_VAL(url->s)) == '/' && *(ZSTR_VAL(url->s)+1) == '/') {
		smart_str_appends(dest, "//");
	}
	if (url_parts->user) {
		smart_str_appends(dest, url_parts->user);
		if (url_parts->pass) {
			smart_str_appends(dest, url_parts->pass);
			smart_str_appendc(dest, ':');
		}
		smart_str_appendc(dest, '@');
	}
	if (url_parts->host) {
				smart_str_appends(dest, url_parts->host);
	}
	if (url_parts->port) {
		smart_str_appendc(dest, ':');
		smart_str_append_unsigned(dest, (long)url_parts->port);
	}
	if (url_parts->path) {
		smart_str_appends(dest, url_parts->path);
	}
	smart_str_appendc(dest, '?');
	if (url_parts->query) {
		smart_str_appends(dest, url_parts->query);
		smart_str_appends(dest, separator);
		smart_str_append_smart_str(dest, url_app);
	} else {
		smart_str_append_smart_str(dest, url_app);
	}
	if (url_parts->fragment) {
		smart_str_appendc(dest, '#');
		smart_str_appends(dest, url_parts->fragment);
	}
	php_url_free(url_parts);
}


#undef YYFILL
#undef YYCTYPE
#undef YYCURSOR
#undef YYLIMIT
#undef YYMARKER

static inline void tag_arg(url_adapt_state_ex_t *ctx, char quotes, char type)
{
	char f = 0;

	if (strncasecmp(ZSTR_VAL(ctx->arg.s), ctx->lookup_data, ZSTR_LEN(ctx->arg.s)) == 0)
		f = 1;

	if (quotes)
		smart_str_appendc(&ctx->result, type);
	if (f) {
		append_modified_url(&ctx->val, &ctx->result, &ctx->url_app, PG(arg_separator).output);
	} else {
		smart_str_append_smart_str(&ctx->result, &ctx->val);
	}
	if (quotes)
		smart_str_appendc(&ctx->result, type);
}

enum {
	STATE_PLAIN = 0,
	STATE_TAG,
	STATE_NEXT_ARG,
	STATE_ARG,
	STATE_BEFORE_VAL,
	STATE_VAL
};

#define YYFILL(n) goto stop
#define YYCTYPE unsigned char
#define YYCURSOR xp
#define YYLIMIT end
#define YYMARKER q
#define STATE ctx->state

#define STD_PARA url_adapt_state_ex_t *ctx, char *start, char *YYCURSOR
#define STD_ARGS ctx, start, xp

#if SCANNER_DEBUG
#define scdebug(x) printf x
#else
#define scdebug(x)
#endif

static inline void passthru(STD_PARA)
{
	scdebug(("appending %d chars, starting with %c\n", YYCURSOR-start, *start));
	smart_str_appendl(&ctx->result, start, YYCURSOR - start);
}

/*
 * This function appends a hidden input field after a <form> or
 * <fieldset>.  The latter is important for XHTML.
 */

static void handle_form(STD_PARA)
{
	int doit = 0;

	if (ZSTR_LEN(ctx->form_app.s) > 0) {
		switch (ZSTR_LEN(ctx->tag.s)) {
			case sizeof("form") - 1:
				if (!strncasecmp(ZSTR_VAL(ctx->tag.s), "form", sizeof("form") - 1)) {
					doit = 1;
				}
				if (doit && ctx->val.s && ctx->lookup_data && *ctx->lookup_data) {
					char *e, *p = (char *)zend_memnstr(ZSTR_VAL(ctx->val.s), "://", sizeof("://") - 1, ZSTR_VAL(ctx->val.s) + ZSTR_LEN(ctx->val.s));
					if (p) {
						e = memchr(p, '/', (ZSTR_VAL(ctx->val.s) + ZSTR_LEN(ctx->val.s)) - p);
						if (!e) {
							e = ZSTR_VAL(ctx->val.s) + ZSTR_LEN(ctx->val.s);
						}
						if ((e - p) && strncasecmp(p, ctx->lookup_data, (e - p))) {
							doit = 0;
						}
					}
				}
				break;

			case sizeof("fieldset") - 1:
				if (!strncasecmp(ZSTR_VAL(ctx->tag.s), "fieldset", sizeof("fieldset") - 1)) {
					doit = 1;
				}
				break;
		}

		if (doit)
			smart_str_append_smart_str(&ctx->result, &ctx->form_app);
	}
}

/*
 *  HANDLE_TAG copies the HTML Tag and checks whether we
 *  have that tag in our table. If we might modify it,
 *  we continue to scan the tag, otherwise we simply copy the complete
 *  HTML stuff to the result buffer.
 */

static inline void handle_tag(STD_PARA)
{
	int ok = 0;
	unsigned int i;

	if (ctx->tag.s) {
		ZSTR_LEN(ctx->tag.s) = 0;
	}
	smart_str_appendl(&ctx->tag, start, YYCURSOR - start);
	for (i = 0; i < ZSTR_LEN(ctx->tag.s); i++)
		ZSTR_VAL(ctx->tag.s)[i] = tolower((int)(unsigned char)ZSTR_VAL(ctx->tag.s)[i]);
    /* intentionally using str_find here, in case the hash value is set, but the string val is changed later */
	if ((ctx->lookup_data = zend_hash_str_find_ptr(ctx->tags, ZSTR_VAL(ctx->tag.s), ZSTR_LEN(ctx->tag.s))) != NULL)
		ok = 1;
	STATE = ok ? STATE_NEXT_ARG : STATE_PLAIN;
}

static inline void handle_arg(STD_PARA)
{
	if (ctx->arg.s) {
		ZSTR_LEN(ctx->arg.s) = 0;
	}
	smart_str_appendl(&ctx->arg, start, YYCURSOR - start);
}

static inline void handle_val(STD_PARA, char quotes, char type)
{
	smart_str_setl(&ctx->val, start + quotes, YYCURSOR - start - quotes * 2);
	tag_arg(ctx, quotes, type);
}

static inline void xx_mainloop(url_adapt_state_ex_t *ctx, const char *newdata, size_t newlen)
{
	char *end, *q;
	char *xp;
	char *start;
	size_t rest;

	smart_str_appendl(&ctx->buf, newdata, newlen);

	YYCURSOR = ZSTR_VAL(ctx->buf.s);
	YYLIMIT = ZSTR_VAL(ctx->buf.s) + ZSTR_LEN(ctx->buf.s);

	switch (STATE) {
		case STATE_PLAIN: goto state_plain;
		case STATE_TAG: goto state_tag;
		case STATE_NEXT_ARG: goto state_next_arg;
		case STATE_ARG: goto state_arg;
		case STATE_BEFORE_VAL: goto state_before_val;
		case STATE_VAL: goto state_val;
	}


state_plain_begin:
	STATE = STATE_PLAIN;

state_plain:
	start = YYCURSOR;

#line 351 "ext/standard/url_scanner_ex.c"
{
	YYCTYPE yych;
	static const unsigned char yybm[] = {
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128,   0, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
	};

	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
	if (yybm[0+yych] & 128) {
		goto yy4;
	}
	++YYCURSOR;
#line 353 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); STATE = STATE_TAG; goto state_tag; }
#line 397 "ext/standard/url_scanner_ex.c"
yy4:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
	if (yybm[0+yych] & 128) {
		goto yy4;
	}
#line 354 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); goto state_plain; }
#line 407 "ext/standard/url_scanner_ex.c"
}
#line 355 "ext/standard/url_scanner_ex.re"


state_tag:
	start = YYCURSOR;

#line 415 "ext/standard/url_scanner_ex.c"
{
	YYCTYPE yych;
	static const unsigned char yybm[] = {
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0, 128,   0,   0,   0,   0,   0, 
		  0, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128,   0,   0,   0,   0,   0, 
		  0, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
	};
	if ((YYLIMIT - YYCURSOR) < 2) YYFILL(2);
	yych = *YYCURSOR;
	if (yych <= '@') {
		if (yych != ':') goto yy11;
	} else {
		if (yych <= 'Z') goto yy9;
		if (yych <= '`') goto yy11;
		if (yych >= '{') goto yy11;
	}
yy9:
	++YYCURSOR;
	yych = *YYCURSOR;
	goto yy14;
yy10:
#line 360 "ext/standard/url_scanner_ex.re"
	{ handle_tag(STD_ARGS); /* Sets STATE */; passthru(STD_ARGS); if (STATE == STATE_PLAIN) goto state_plain; else goto state_next_arg; }
#line 468 "ext/standard/url_scanner_ex.c"
yy11:
	++YYCURSOR;
#line 361 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); goto state_plain_begin; }
#line 473 "ext/standard/url_scanner_ex.c"
yy13:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy14:
	if (yybm[0+yych] & 128) {
		goto yy13;
	}
	goto yy10;
}
#line 362 "ext/standard/url_scanner_ex.re"


state_next_arg_begin:
	STATE = STATE_NEXT_ARG;

state_next_arg:
	start = YYCURSOR;

#line 493 "ext/standard/url_scanner_ex.c"
{
	YYCTYPE yych;
	static const unsigned char yybm[] = {
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0, 128, 128, 128,   0, 128,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		128,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
	};
	if ((YYLIMIT - YYCURSOR) < 2) YYFILL(2);
	yych = *YYCURSOR;
	if (yych <= '.') {
		if (yych <= '\f') {
			if (yych <= 0x08) goto yy25;
			if (yych <= '\v') goto yy21;
			goto yy25;
		} else {
			if (yych <= '\r') goto yy21;
			if (yych == ' ') goto yy21;
			goto yy25;
		}
	} else {
		if (yych <= '@') {
			if (yych <= '/') goto yy17;
			if (yych == '>') goto yy19;
			goto yy25;
		} else {
			if (yych <= 'Z') goto yy23;
			if (yych <= '`') goto yy25;
			if (yych <= 'z') goto yy23;
			goto yy25;
		}
	}
yy17:
	++YYCURSOR;
	if ((yych = *YYCURSOR) == '>') goto yy28;
yy18:
#line 373 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); goto state_plain_begin; }
#line 560 "ext/standard/url_scanner_ex.c"
yy19:
	++YYCURSOR;
yy20:
#line 370 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); handle_form(STD_ARGS); goto state_plain_begin; }
#line 566 "ext/standard/url_scanner_ex.c"
yy21:
	++YYCURSOR;
	yych = *YYCURSOR;
	goto yy27;
yy22:
#line 371 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); goto state_next_arg; }
#line 574 "ext/standard/url_scanner_ex.c"
yy23:
	++YYCURSOR;
#line 372 "ext/standard/url_scanner_ex.re"
	{ --YYCURSOR; STATE = STATE_ARG; goto state_arg; }
#line 579 "ext/standard/url_scanner_ex.c"
yy25:
	yych = *++YYCURSOR;
	goto yy18;
yy26:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy27:
	if (yybm[0+yych] & 128) {
		goto yy26;
	}
	goto yy22;
yy28:
	++YYCURSOR;
	yych = *YYCURSOR;
	goto yy20;
}
#line 374 "ext/standard/url_scanner_ex.re"


state_arg:
	start = YYCURSOR;

#line 603 "ext/standard/url_scanner_ex.c"
{
	YYCTYPE yych;
	static const unsigned char yybm[] = {
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0, 128,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128,   0,   0,   0,   0,   0, 
		  0, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128, 128, 128, 128, 128, 128, 
		128, 128, 128,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
	};
	if ((YYLIMIT - YYCURSOR) < 2) YYFILL(2);
	yych = *YYCURSOR;
	if (yych <= '@') goto yy33;
	if (yych <= 'Z') goto yy31;
	if (yych <= '`') goto yy33;
	if (yych >= '{') goto yy33;
yy31:
	++YYCURSOR;
	yych = *YYCURSOR;
	goto yy36;
yy32:
#line 379 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); handle_arg(STD_ARGS); STATE = STATE_BEFORE_VAL; goto state_before_val; }
#line 653 "ext/standard/url_scanner_ex.c"
yy33:
	++YYCURSOR;
#line 380 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); STATE = STATE_NEXT_ARG; goto state_next_arg; }
#line 658 "ext/standard/url_scanner_ex.c"
yy35:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy36:
	if (yybm[0+yych] & 128) {
		goto yy35;
	}
	goto yy32;
}
#line 381 "ext/standard/url_scanner_ex.re"


state_before_val:
	start = YYCURSOR;

#line 675 "ext/standard/url_scanner_ex.c"
{
	YYCTYPE yych;
	static const unsigned char yybm[] = {
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		128,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
		  0,   0,   0,   0,   0,   0,   0,   0, 
	};
	if ((YYLIMIT - YYCURSOR) < 2) YYFILL(2);
	yych = *YYCURSOR;
	if (yych == ' ') goto yy39;
	if (yych == '=') goto yy41;
	goto yy43;
yy39:
	yych = *(YYMARKER = ++YYCURSOR);
	if (yych == ' ') goto yy46;
	if (yych == '=') goto yy44;
yy40:
#line 387 "ext/standard/url_scanner_ex.re"
	{ --YYCURSOR; goto state_next_arg_begin; }
#line 724 "ext/standard/url_scanner_ex.c"
yy41:
	++YYCURSOR;
	yych = *YYCURSOR;
	goto yy45;
yy42:
#line 386 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); STATE = STATE_VAL; goto state_val; }
#line 732 "ext/standard/url_scanner_ex.c"
yy43:
	yych = *++YYCURSOR;
	goto yy40;
yy44:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy45:
	if (yybm[0+yych] & 128) {
		goto yy44;
	}
	goto yy42;
yy46:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
	if (yych == ' ') goto yy46;
	if (yych == '=') goto yy44;
	YYCURSOR = YYMARKER;
	goto yy40;
}
#line 388 "ext/standard/url_scanner_ex.re"



state_val:
	start = YYCURSOR;

#line 761 "ext/standard/url_scanner_ex.c"
{
	YYCTYPE yych;
	static const unsigned char yybm[] = {
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 192, 192, 224, 224, 192, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		192, 224,  64, 224, 224, 224, 224, 128, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224,   0, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
		224, 224, 224, 224, 224, 224, 224, 224, 
	};
	if ((YYLIMIT - YYCURSOR) < 2) YYFILL(2);
	yych = *YYCURSOR;
	if (yych <= ' ') {
		if (yych <= '\f') {
			if (yych <= 0x08) goto yy54;
			if (yych <= '\n') goto yy56;
			goto yy54;
		} else {
			if (yych <= '\r') goto yy56;
			if (yych <= 0x1F) goto yy54;
			goto yy56;
		}
	} else {
		if (yych <= '&') {
			if (yych != '"') goto yy54;
		} else {
			if (yych <= '\'') goto yy53;
			if (yych == '>') goto yy56;
			goto yy54;
		}
	}
	yych = *(YYMARKER = ++YYCURSOR);
	if (yych != '>') goto yy65;
yy52:
#line 397 "ext/standard/url_scanner_ex.re"
	{ passthru(STD_ARGS); goto state_next_arg_begin; }
#line 824 "ext/standard/url_scanner_ex.c"
yy53:
	yych = *(YYMARKER = ++YYCURSOR);
	if (yych == '>') goto yy52;
	goto yy60;
yy54:
	++YYCURSOR;
	yych = *YYCURSOR;
	goto yy58;
yy55:
#line 396 "ext/standard/url_scanner_ex.re"
	{ handle_val(STD_ARGS, 0, ' '); goto state_next_arg_begin; }
#line 836 "ext/standard/url_scanner_ex.c"
yy56:
	yych = *++YYCURSOR;
	goto yy52;
yy57:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy58:
	if (yybm[0+yych] & 32) {
		goto yy57;
	}
	goto yy55;
yy59:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy60:
	if (yybm[0+yych] & 64) {
		goto yy59;
	}
	if (yych <= '\'') goto yy62;
yy61:
	YYCURSOR = YYMARKER;
	goto yy52;
yy62:
	++YYCURSOR;
#line 395 "ext/standard/url_scanner_ex.re"
	{ handle_val(STD_ARGS, 1, '\''); goto state_next_arg_begin; }
#line 865 "ext/standard/url_scanner_ex.c"
yy64:
	++YYCURSOR;
	if (YYLIMIT <= YYCURSOR) YYFILL(1);
	yych = *YYCURSOR;
yy65:
	if (yybm[0+yych] & 128) {
		goto yy64;
	}
	if (yych >= '#') goto yy61;
	++YYCURSOR;
#line 394 "ext/standard/url_scanner_ex.re"
	{ handle_val(STD_ARGS, 1, '"'); goto state_next_arg_begin; }
#line 878 "ext/standard/url_scanner_ex.c"
}
#line 398 "ext/standard/url_scanner_ex.re"


stop:
	if (YYLIMIT < start) {
		/* XXX: Crash avoidance. Need to work with reporter to figure out what goes wrong */
		rest = 0;
	} else {
		rest = YYLIMIT - start;
		scdebug(("stopped in state %d at pos %d (%d:%c) %d\n", STATE, YYCURSOR - ctx->buf.c, *YYCURSOR, *YYCURSOR, rest));
	}

	if (rest) memmove(ZSTR_VAL(ctx->buf.s), start, rest);
	ZSTR_LEN(ctx->buf.s) = rest;
}


PHPAPI char *php_url_scanner_adapt_single_url(const char *url, size_t urllen, const char *name, const char *value, size_t *newlen, int urlencode)
{
	char *result;
	smart_str surl = {0};
	smart_str buf = {0};
	smart_str url_app = {0};
	zend_string *encoded;

	smart_str_appendl(&surl, url, urllen);

	if (urlencode) {
		encoded = php_raw_url_encode(name, strlen(name));
		smart_str_appendl(&url_app, ZSTR_VAL(encoded), ZSTR_LEN(encoded));
		zend_string_free(encoded);
	} else {
		smart_str_appends(&url_app, name);
	}
	smart_str_appendc(&url_app, '=');
	if (urlencode) {
		encoded = php_raw_url_encode(value, strlen(value));
		smart_str_appendl(&url_app, ZSTR_VAL(encoded), ZSTR_LEN(encoded));
		zend_string_free(encoded);
	} else {
		smart_str_appends(&url_app, value);
	}

	append_modified_url(&surl, &buf, &url_app, PG(arg_separator).output);

	smart_str_0(&buf);
	if (newlen) *newlen = ZSTR_LEN(buf.s);
	result = estrndup(ZSTR_VAL(buf.s), ZSTR_LEN(buf.s));

	smart_str_free(&url_app);
	smart_str_free(&buf);

	return result;
}


static char *url_adapt_ext(const char *src, size_t srclen, size_t *newlen, zend_bool do_flush)
{
	url_adapt_state_ex_t *ctx;
	char *retval;

	ctx = &BG(url_adapt_state_ex);

	xx_mainloop(ctx, src, srclen);

	if (!ctx->result.s) {
		smart_str_appendl(&ctx->result, "", 0);
		*newlen = 0;
	} else {
		*newlen = ZSTR_LEN(ctx->result.s);
	}
	smart_str_0(&ctx->result);
	if (do_flush) {
		smart_str_append(&ctx->result, ctx->buf.s);
		*newlen += ZSTR_LEN(ctx->buf.s);
		smart_str_free(&ctx->buf);
		smart_str_free(&ctx->val);
	}
	retval = estrndup(ZSTR_VAL(ctx->result.s), ZSTR_LEN(ctx->result.s));
	smart_str_free(&ctx->result);
	return retval;
}

static int php_url_scanner_ex_activate(void)
{
	url_adapt_state_ex_t *ctx;

	ctx = &BG(url_adapt_state_ex);

	memset(ctx, 0, ((size_t) &((url_adapt_state_ex_t *)0)->tags));

	return SUCCESS;
}

static int php_url_scanner_ex_deactivate(void)
{
	url_adapt_state_ex_t *ctx;

	ctx = &BG(url_adapt_state_ex);

	smart_str_free(&ctx->result);
	smart_str_free(&ctx->buf);
	smart_str_free(&ctx->tag);
	smart_str_free(&ctx->arg);

	return SUCCESS;
}

static void php_url_scanner_output_handler(char *output, size_t output_len, char **handled_output, size_t *handled_output_len, int mode)
{
	size_t len;

	if (ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) != 0) {
		*handled_output = url_adapt_ext(output, output_len, &len, (zend_bool) (mode & (PHP_OUTPUT_HANDLER_END | PHP_OUTPUT_HANDLER_CONT | PHP_OUTPUT_HANDLER_FLUSH | PHP_OUTPUT_HANDLER_FINAL) ? 1 : 0));
		if (sizeof(uint) < sizeof(size_t)) {
			if (len > UINT_MAX)
				len = UINT_MAX;
		}
		*handled_output_len = len;
	} else if (ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) == 0) {
		url_adapt_state_ex_t *ctx = &BG(url_adapt_state_ex);
		if (ctx->buf.s && ZSTR_LEN(ctx->buf.s)) {
			smart_str_append(&ctx->result, ctx->buf.s);
			smart_str_appendl(&ctx->result, output, output_len);

			*handled_output = estrndup(ZSTR_VAL(ctx->result.s), ZSTR_LEN(ctx->result.s));
			*handled_output_len = ZSTR_LEN(ctx->buf.s) + output_len;

			smart_str_free(&ctx->buf);
			smart_str_free(&ctx->result);
		} else {
			*handled_output = estrndup(output, *handled_output_len = output_len);
		}
	} else {
		*handled_output = NULL;
	}
}

PHPAPI int php_url_scanner_add_var(char *name, size_t name_len, char *value, size_t value_len, int urlencode)
{
	smart_str sname = {0};
	smart_str svalue = {0};
	zend_string *encoded;

	if (!BG(url_adapt_state_ex).active) {
		php_url_scanner_ex_activate();
		php_output_start_internal(ZEND_STRL("URL-Rewriter"), php_url_scanner_output_handler, 0, PHP_OUTPUT_HANDLER_STDFLAGS);
		BG(url_adapt_state_ex).active = 1;
	}

	if (BG(url_adapt_state_ex).url_app.s && ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) != 0) {
		smart_str_appends(&BG(url_adapt_state_ex).url_app, PG(arg_separator).output);
	}

	if (urlencode) {
		encoded = php_raw_url_encode(name, name_len);
		smart_str_appendl(&sname, ZSTR_VAL(encoded), ZSTR_LEN(encoded));
		zend_string_free(encoded);
		encoded = php_raw_url_encode(value, value_len);
		smart_str_appendl(&svalue, ZSTR_VAL(encoded), ZSTR_LEN(encoded));
		zend_string_free(encoded);
	} else {
		smart_str_appendl(&sname, name, name_len);
		smart_str_appendl(&svalue, value, value_len);
	}

	smart_str_append_smart_str(&BG(url_adapt_state_ex).url_app, &sname);
	smart_str_appendc(&BG(url_adapt_state_ex).url_app, '=');
	smart_str_append_smart_str(&BG(url_adapt_state_ex).url_app, &svalue);

	smart_str_appends(&BG(url_adapt_state_ex).form_app, "<input type=\"hidden\" name=\"");
	smart_str_append_smart_str(&BG(url_adapt_state_ex).form_app, &sname);
	smart_str_appends(&BG(url_adapt_state_ex).form_app, "\" value=\"");
	smart_str_append_smart_str(&BG(url_adapt_state_ex).form_app, &svalue);
	smart_str_appends(&BG(url_adapt_state_ex).form_app, "\" />");

	smart_str_free(&sname);
	smart_str_free(&svalue);

	return SUCCESS;
}

static inline void php_url_scanner_clear(void) {
	if (BG(url_adapt_state_ex).form_app.s) {
		ZSTR_LEN(BG(url_adapt_state_ex).form_app.s) = 0;
	}
	if (BG(url_adapt_state_ex).url_app.s) {
		ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) = 0;
	}
}

PHPAPI int php_url_scanner_reset_vars(void)
{
	php_url_scanner_clear();
	return SUCCESS;
}

PHPAPI int php_url_scanner_reset_var(zend_string *name, int urlencode)
{
	char *start;
	char *end;
	size_t separator_len;
	smart_str sname = {0};
	smart_str svalue = {0};
	smart_str url_app = {0};
	smart_str form_app = {0};
	zend_string *encoded;
	int ret = SUCCESS;
	zend_bool sep_removed = 0;

	/* Short circuit check. Only check url_app. */
	if (!BG(url_adapt_state_ex).url_app.s || !ZSTR_LEN(BG(url_adapt_state_ex).url_app.s)) {
		return SUCCESS;
	}

	if (urlencode) {
		encoded = php_raw_url_encode(ZSTR_VAL(name), ZSTR_LEN(name));
		smart_str_appendl(&sname, ZSTR_VAL(encoded), ZSTR_LEN(encoded));
		zend_string_free(encoded);
	} else {
		smart_str_appendl(&sname, ZSTR_VAL(name), ZSTR_LEN(name));
	}
	smart_str_0(&sname);

	smart_str_append_smart_str(&url_app, &sname);
	smart_str_appendc(&url_app, '=');
	smart_str_0(&url_app);

	smart_str_appends(&form_app, "<input type=\"hidden\" name=\"");
	smart_str_append_smart_str(&form_app, &sname);
	smart_str_appends(&form_app, "\" value=\"");
	smart_str_0(&form_app);

	/* Short circuit check. Only check url_app. */
	start = (char *) php_memnstr(ZSTR_VAL(BG(url_adapt_state_ex).url_app.s),
						ZSTR_VAL(url_app.s), ZSTR_LEN(url_app.s),
						ZSTR_VAL(BG(url_adapt_state_ex).url_app.s) + ZSTR_LEN(BG(url_adapt_state_ex).url_app.s));
	if (!start) {
		ret = FAILURE;
		goto finish;
	}

	/* Get end of url var */
	end = start + ZSTR_LEN(url_app.s);
	separator_len = strlen(PG(arg_separator).output);
	ZEND_ASSERT(!ZSTR_VAL(BG(url_adapt_state_ex).url_app.s)[ZSTR_LEN(BG(url_adapt_state_ex).url_app.s)]);
	while (1) {
		if (!*end) {
			break;
		}
		if (!memcmp(end, PG(arg_separator).output, separator_len)) {
			end += separator_len;
			sep_removed = 1;
			break;
		}
		end++;
	}
	/* Remove all when this is the only rewrite var */
	if (ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) == end - start) {
		php_url_scanner_clear();
		goto finish;
	}
    /* Check preceeding separator */
    if (!sep_removed
		&& start - PG(arg_separator).output >= separator_len
		&& !memcmp(start - separator_len, PG(arg_separator).output, separator_len)) {
		start -= separator_len;
	}
	/* Remove partially */
	memmove(start, end,
			ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) - (end - ZSTR_VAL(BG(url_adapt_state_ex).url_app.s)));
	ZSTR_LEN(BG(url_adapt_state_ex).url_app.s) -= end - start;
	ZSTR_VAL(BG(url_adapt_state_ex).url_app.s)[ZSTR_LEN(BG(url_adapt_state_ex).url_app.s)] = '\0';

	/* Remove form var */
	start = (char *) php_memnstr(ZSTR_VAL(BG(url_adapt_state_ex).form_app.s),
						ZSTR_VAL(form_app.s), ZSTR_LEN(form_app.s),
						ZSTR_VAL(BG(url_adapt_state_ex).form_app.s) + ZSTR_LEN(BG(url_adapt_state_ex).form_app.s));
	if (!start) {
		/* Should not happen */
		ret = FAILURE;
		php_url_scanner_clear();
		goto finish;
	}
	/* Get end of form var */
	end = start + ZSTR_LEN(form_app.s);
	ZEND_ASSERT(!ZSTR_VAL(BG(url_adapt_state_ex).form_app.s)[ZSTR_LEN(BG(url_adapt_state_ex).form_app.s)]);
	while (1) {
		if (!*end) {
			break;
		}
		if (*end == '>') {
			end += 1;
			break;
		}
		end++;
	}
	/* Remove partially */
	memmove(start, end,
			ZSTR_LEN(BG(url_adapt_state_ex).form_app.s) - (end - ZSTR_VAL(BG(url_adapt_state_ex).form_app.s)));
	ZSTR_LEN(BG(url_adapt_state_ex).form_app.s) -= end - start;
	ZSTR_VAL(BG(url_adapt_state_ex).form_app.s)[ZSTR_LEN(BG(url_adapt_state_ex).form_app.s)] = '\0';

finish:
	smart_str_free(&url_app);
	smart_str_free(&form_app);
	smart_str_free(&sname);
	smart_str_free(&svalue);
	return ret;
}


PHP_MINIT_FUNCTION(url_scanner)
{
	BG(url_adapt_state_ex).tags = NULL;

	BG(url_adapt_state_ex).form_app.s = BG(url_adapt_state_ex).url_app.s = NULL;

	REGISTER_INI_ENTRIES();
	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(url_scanner)
{
	UNREGISTER_INI_ENTRIES();

	return SUCCESS;
}

PHP_RINIT_FUNCTION(url_scanner)
{
	BG(url_adapt_state_ex).active = 0;

	return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(url_scanner)
{
	if (BG(url_adapt_state_ex).active) {
		php_url_scanner_ex_deactivate();
		BG(url_adapt_state_ex).active = 0;
	}

	smart_str_free(&BG(url_adapt_state_ex).form_app);
	smart_str_free(&BG(url_adapt_state_ex).url_app);

	return SUCCESS;
}
