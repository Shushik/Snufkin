
	Snufkin, the OOP wrapper for PHP lib_curl


	Goods

	— Simple syntax;
	— Methods chaining support;
	— Parsing of common http-request info into OOP-interface;
	— Cookies support;
	— Simple SSL support;
	— Response body encoding;
	— User-defined extra-parsers are available.


	System requirements

	– PHP ≥ 5;
	– libcurl;
	– iconv.


	Config options

	 Alias     | Type     | Description
	=================================================================================
	 timeout   | Optional | timeout for waiting in seconds
	 redirects | Optional | maximum number of redirects lib_curl will follow
	 agent     | Optional | one of the keywords for the real user agent emulation
	 referer   | Optional | referer link for the real browser emulation too
	 cookies   | Optional | absolute path to cookie.jar file (with a file name)
	           |          | you will need to set 777 permission on a directory with
	           |          | a cookie-file
	 charset   | Optional | default charset for encoding
	 encoding  | Optional | usually it`s gzip/deflate
	 headers   | Optional | common request headers
	 ssl       | Optional | true to use default ssl settings or an array with custom
	           |          | ssl settings
	=================================================================================


	SSL settings

	 Alias   | Type     | Description
	==========================================================
	 version | Optional | ssl protocol version
	 peer    | Optional | false to prohibit sertificate check
	         |          | true in other case
	 host    | Optional | false to prohibit host check
	         |          | 1 to check host existing only
	         |          | 2 to check host host name
	 cert    | Optional | full path to sertificate .pem file
	         |          | (use with the «pass» option)
	 pass    | Optional | password for the .pem sertificate
	==========================================================


	List of available user agents

	 Alias         | OS       | Browser
	================================================
	 win.ie.5      | Windows  | Internet Explorer 5
	 win.ie.6      | Windows  | Internet Explorer 6
	 win.ie.7      | Windows  | Internet Explorer 7
	 win.ie.8      | Windows  | Internet Explorer 8
	 win.ie.9      | Windows  | Internet Explorer 9
	 win.ff.3      | Windows  | Firefox 3.0.6
	 win.ff.4      | Windows  | Firefox 4.0.1
	 win.ff.5      | Windows  | Firefox 5.0
	 win.opera.9   | Windows  | Opera 9.63
	 win.opera.11  | Windows  | Opera 11.50
	 win.safari.3  | Windows  | Safari 3.1.2
	 win.chrome.1  | Windows  | Chrome 1.0
	------------------------------------------------
	 lin.ff.3      | Linux    | Firefox 3.1;
	 lin.kq.3      | Linux    | Conqueror 3.5.10;
	 lin.opera.9   | Linux    | Opera 9.63;
	------------------------------------------------
	 bsd.lynx      | Free BSD | Lynx
	 bsd.links     | Free BSD | Links
	------------------------------------------------
	 mac.ff.3      | Mac OS   | Firefox 3.0.6
	 mac.ff.5      | Mac OS   | Firefox 5.0.1
	 mac.opera.9   | Mac OS   | Opera 9.62
	 mac.opera.11  | Mac OS   | Opera 11.50
	 mac.chrome.12 | Mac OS   | Chrome 12.0
	 mac.safari.3  | Mac OS   | Safari 3.2.1
	 mac.safari.5  | Mac OS   | Safari 5.1
	================================================


	Code examples


	 1. Simple init

	<code>
		$Browser = new Snufkin;
		$Browser->get('http://www.google.com');

		$Browser->dump_get();
	</code>


	 2. Init using the custom config

	<code>
		$conf = array(
			'timeout'   => 5,
			'redirects' => 10,
			'agent'     => 'win.ff.3',
			'referer'   => 'http://www.google.com/',
			'encoding'  => 'gzip,deflate',
			'headers'   => array(
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
				'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
				'Connection: keep-alive"',
				'Keep-Alive: 300',
				'Cache-Control: max-age=0',
				'Pragma: ',
			),
		);

		$Browser = new Snufkin($conf);
		$Browser->get('http://www.google.com');

		$Browser->dump_get();
	</code>


	 3. Make a GET-request

	<code>
		$Browser->get('http://facebook.com');

		$Browser->dump_get();
	</code>

	Params for get_request_send()

	     | Param    | Description
	=============================================================
	  1. | URL      | site url
	  2. | nobody   | true if you want headers only,
	     |          | false in other case
	  5. | raw_save | true if you want to save unparsed response
	=============================================================


	 4. Make a POST-request

	<code>
		$Browser->post(
			'http://facebook.com',
			array(
				'param1' => 'value1',
				'param2' => 'value2',
			)
		);

		$Browser->dump_get();
	</code>

	Params for post_request_send()

	     | Param    | Description
	=============================================================
	  1. | URL      | site url
	  2. | params   | POST params array
	  3. | nobody   | true if you want headers only,
	     |          | false in other case
	  6. | raw_save | true if you want to save unparsed response
	=============================================================


	 5. Make a HEAD-request

	<code>
		$Browser->get('http://www.facebook.com', true);

		$Browser->dump_get();
	</code>


	 6. Get the section of http-response

	<code>
		$Browser->get('http://facebook.com');

		$raw     = $Browser->raw();
		$body    = $Browser->body();
		$heads   = $Browser->heads();
		$head    = $Browser->head();
		$headers = $Browser->headers();
		$header  = $Browser->header('Content-Type');
		$cookies = $Browser->cookies();
		$cookie  = $Browser->cookie('lsd');

		$Browser->dump_get();
	</code>

	Sections methods description

	     | Method    | Description
	===============================================================
	  1. | raw()     | unparsed response (exists only if you`ve
	     |           | set raw_save param using get() or post()
	     |           | methods)
	  2. | body()    | response body
	  3. | heads()   | headers for all redirects
	  4. | head()    | headers for the last response
	  5. | http()    | http section of head()
	  6. | headers() | headers section of head()
	  7. | header()  | value of the header given in param or false
	  7. | cookies() | cookies section of head()
	  8. | cookie()  | value of the cookie given in param or false
	===============================================================


	 7. Change the response encoding:

	<code>
		$Browser->get('http://facebook.com')->charset('utf-8', 'windows-1251');

		$Browser->dump_get();
	</code>

	Params for charset()

	     | Param    | Description
	=================================================================
	  1. | given    | given encoding, use false to take it from
	     |          | response Charset header
	  2. | needed   | encoding you need to encode the response body,
	     |          | use false to take it from the common config
	=================================================================


	 8. Clean response body from line skews, tabs and spaces

	<code>
		$Browser->get('http://facebook.com')->trim();

		$Browser->dump_get();
	</code>


	 9. Make a https request using default SSL settings

	<code>
		$conf = array(
			'timeout'   => 5,
			'redirects' => 10,
			'agent'     => 'win.ff.3',
			'referer'   => 'http://www.google.com/',
			'ssl'       => true,
		);

		$Browser = new Snufkin($conf);

		$Browser->get(
			'https://somehost.com'
		);

		$Browser->dump_get();
	</code>


	10. Make a https request using custom SSL settings

	<code>
		$conf = array(
			'timeout'   => 5,
			'redirects' => 10,
			'agent'     => 'win.ff.3',
			'referer'   => 'http://www.google.com/',
			'ssl'       => array(
				'version' => 2,
				'host'    => 1,
				'peer'    => true,
			),
		);

		$Browser = new Snufkin($conf);

		$Browser->get('https://somehost.com');

		$Browser->dump_get();
	</code>