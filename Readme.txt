
	Snufkin, the OOP wrapper for PHP lib_curl


	Goods

	— Simple sintax;
	— Parsing of common http-request info into OOP-interface;
	— Cookies support;
	— Response body encoding.


	System requirements

	– PHP ≥ 5;
	– libcurl;
	– iconv.


	Config options

	 Alias     | Description
	=====================================================================
	 timeout   | timeout for waiting in seconds
	 redirects | maximum number of redirects lib_curl will follow
	 agent     | one of the keywords for the real user agent emulation
	 referer   | referer link for the real browser emulation too
	 cookies   | absolute path to cookie.jar file (with a file name)
	           | you will need to set 777 permission on a directory with
	           | a cookie-file
	 charset   | default charset for encoding
	 encoding  | usually it`s gzip/deflate
	 headers   | common request headers
	=====================================================================


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
	 win.opera.9   | Windows  | Opera 9.63
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


	 1. Create an object:

	<code>
		$conf = array(
			'timeout'   => 5,
			'redirects' => 10,
			'agent'     => 'win.ff.3',
			'referer'   => 'http://www.google.com/',
			'encoding'  => 'gzip,deflate',
			'headers']  => array(
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
	</code>


	 2. Make a GET-request:

	<code>
		$Browser->get_request_send(
			'http://facebook.com'
		);

		$Browser->dump_get();
	</code>

	Params for get_request_send()

	     | Param    | Description
	=============================================================
	  1. | URL      | site url
	  2. | nobody   | true if you want headers only,
	     |          | false in other case
	  3. | headers  | custom headers for the current request
	     |          | or false
	  4. | referer  | custom referer for the current request
	     |          | or false
	  5. | raw_save | true if you want to save unparsed response
	=============================================================


	 3. Make a POST-request:

	<code>
		$Browser->post_request_send(
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
	  4. | headers  | custom headers for the current request
	     |          | or false
	  5. | referer  | custom referer for the current request
	     |          | or false
	  6. | raw_save | true if you want to save unparsed response
	=============================================================


	 4. Change the response encoding:

	<code>
		$Browser->get_request_send(
			'http://facebook.com'
		);

		$Browser->response_charset_change('utf-8', 'windows-1251');

		$Browser->dump_get();
	</code>

	Params for response_charset_change()

	     | Param    | Description
	=================================================================
	  1. | given    | given encoding, use false to take it from
	     |          | response Charset header
	  2. | needed   | encoding you need to encode the response body,
	     |          | use false to take it from the common config
	=================================================================


	 5. Clean response body from line skews, tabs and spaces

	<code>
		$Browser->get_request_send(
			'http://facebook.com'
		);

		$Browser->response_body_clean();

		$Browser->dump_get();
	</code>