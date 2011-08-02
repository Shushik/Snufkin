<?


	/**
	 * Snufkin
	 *
	 * OOP wrapper for PHP lib_curl
	 *
	 * @author  Shushik <silkleopard@yandex.ru>
	 * @version 3.3
	 */
	class Snufkin {

		public
			$ready    = false,
			$request  = null,
			$response = null;

		private
			$temp    = false,
			$common  = null,
			$handler = null;

		private static
			$agents = array(
				'win.ie.5'      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; SV1)',
				'win.ie.6'      => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)',
				'win.ie.7'      => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
				'win.ie.8'      => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
				'win.ie.9'      => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',

				'win.ff.3'      => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6',
				'win.ff.4'      => 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
				'win.ff.5'      => 'Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.0',
				'win.opera.9'   => 'Opera/9.63 (Windows NT 5.1; U; en)',
				'win.opera.11'  => 'Opera/9.80 (Windows NT 5.1; U; en) Presto/2.9.168 Version/11.50',
				'win.safari.3'  => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Version/3.1.2 Safari/525.21',
				'win.chrome.1'  => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.48 Safari/525.19',

				'lin.ff'        => 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1b3pre) Gecko/20090217 Shiretoko/3.1b3pre',
				'lin.kq.3'      => 'Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.10 (like Gecko)',
				'lin.opera.9'   => 'Opera/9.63 (X11; Linux i686; U; en) Presto/2.1.1',

				'bsd.lynx'      => 'Lynx/2.8.6rel.5 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.7e-p1',
				'bsd.links'     => 'Links (2.2; FreeBSD 7.0-RC1 i386; 195x65)',

				'mac.ff.3'      => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.6) Gecko/2009011912 Firefox/3.0.6',
				'mac.ff.5'      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
				'mac.opera.9'   => 'Opera/9.62 (Macintosh; Intel Mac OS X; U; en) Presto/2.1.1',
				'mac.opera.11'  => 'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.9.168 Version/11.50',
				'mac.chrome.12' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30',
				'mac.safari.3'  => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1',
				'mac.safari.5'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
			);


		/**
		 * Create a link to lib_curl, set up basic lib_curl settings
		 *
		 * @constructor
		 * @method
		 *
		 * @param boolean|string|array $param_0
		 *
		 * @return object
		 */
		function
			__construct($conf = false) {
				$conf = array(
					'timeout'   => 5,
					'redirects' => 2,
					'agent'     => 'Snufkin 4.0',
					'referer'   => 'http://github.com/Shushik/Snufkin/',
					'charset'   => 'utf-8',
					'encoding'  => 'gzip/deflate',
					'ssl'       => array(),
					'headers'   => array(),
				);

				if ($conf) {
					// Change default config values
					foreach ($conf as $alias => $value) {
						$conf[$alias] = $value;
					}
				}

				// Initiate the connection to the curl
				$this->handler = curl_init();

				if (is_resource($this->handler)) {
					// Turn the ready state identifyer to «on»
					$this->ready = true;

					// Create a common properties object
					$this->common = new stdClass;
					$this->common->charset = $conf['charset'];
					$this->common->referer = $conf['referer'];
					$this->common->headers = $conf['headers'];

					// Set up the basic curl_lib settings
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_HEADER            => true,
							CURLOPT_TIMEOUT           => $conf['timeout'],
							CURLOPT_REFERER           => $conf['referer'],
							CURLOPT_ENCODING          => $conf['encoding'],
							CURLOPT_USERAGENT         => $conf['agent'],
							CURLOPT_MAXREDIRS         => $conf['redirects'],
							CURLOPT_HTTPHEADER        => $conf['headers'],
							CURLOPT_AUTOREFERER       => true,
							CURLOPT_RETURNTRANSFER    => true,
							CURLOPT_FOLLOWLOCATION    => true,
							CURLOPT_UNRESTRICTED_AUTH => true,
						)
					);

					// Turn on lib_curl cookies if the jar file link given
					if (@$conf['cookies']) {
						$this->cookies_setup($conf['cookies']);
					}

					// Turn on SSL and apply SSL-settings
					if (@$conf['ssl']) {
						$this->ssl_setup($conf['ssl']);
					}
				}
			}

		/**
		 * Check if connection with curl is alive and close it
		 *
		 * @destructor
		 * @method
		 */
		function
			__destruct() {
				if (is_resource($this->handler)) {
					curl_close($this->handler);
				}
			}

		/**
		 * Turn on cookies and set up cookies support
		 *
		 * @private
		 * @method
		 *
		 * @param string $path
		 */
		private function
			cookies_setup($path) {
				if (file_exists($path)) {
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_COOKIEJAR  => $path,
							CURLOPT_COOKIEFILE => $path,
						)
					);
				}
			}

		/**
		 * Turn on ssl and set up ssl support
		 *
		 * @private
		 * @method
		 *
		 * @param string $path
		 */
		private function
			ssl_setup($conf) {
				switch (gettype($conf['ssl'])) {

					// Apply default settings for ssl
					case 'boolean':
					case 'integer':
						curl_setopt_array(
							$this->handler,
							array(
								CURLOPT_SSLVERSION     => 3,
								CURLOPT_SSL_VERIFYPEER => false,
								CURLOPT_SSL_VERIFYHOST => false,
							)
						);
					break;

					// Apply custom settings
					case 'array':
						// Set the sertificate check
						if ($conf['ssl']['peer']) {
							curl_setopt(
								$this->handler,
								CURLOPT_SSL_VERIFYPEER,
								$conf['ssl']['peer']
							);
						}

						// Set the host check
						if ($conf['ssl']['host']) {
							curl_setopt(
								$this->handler,
								CURLOPT_SSL_VERIFYHOST,
								$conf['ssl']['host']
							);
						}

						// Set the protocol version
						if ($conf['ssl']['version']) {
							curl_setopt(
								$this->handler,
								CURLOPT_SSLVERSION,
								$conf['ssl']['version']
							);
						}

						// Set the ssl-sertificate path
						if ($conf['ssl']['cert']) {
							curl_setopt(
								$this->handler,
								CURLOPT_SSLCERT,
								$conf['ssl']['cert']
							);
						}

						// Set the ssl-sertificate password
						if ($conf['ssl']['cert']) {
							curl_setopt(
								$this->handler,
								CURLOPT_SSLCERTPASSWD,
								$conf['ssl']['pass']
							);
						}
					break;

				}
			}

		/**
		 * Reset common settings for HTTP request
		 *
		 * @public
		 * @method
		 *
		 * @param string       $section
		 * @param string|array $value
		 *
		 * @return object
		 */
		public function
			http_request_set($section, $value) {
				if ($this->ready) {
					switch ($section) {

						case 'referer':
							curl_setopt($this->handler, CURLOPT_REFERER, $value);
						break;

						case 'headers':
							curl_setopt($this->handler, CURLOPT_HTTPHEADER, $value);
						break;

					}

					$this->temp = true;

					return $this;
				}
			}

		/**
		 * Reset common settings for HTTP request
		 *
		 * @private
		 * @method
		 *
		 * @return object
		 */
		private function
			http_request_unset() {
				if ($this->ready) {
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_REFERER    => $this->common->referer,
							CURLOPT_HTTPHEADER => $this->commmon->headers,
						)
					);

					$this->temp = false;
				}
			}

		/**
		 * Send a HTTP request
		 *
		 * @public
		 * @method
		 *
		 * @param string  $url
		 * @param boolean $no_body
		 * @param boolean $raw_save
		 *
		 * @return object
		 */
		public function
			http_request_send($url, $no_body = false, $raw_save = false) {
				if ($this->ready) {
					// Set up curl_lib settings for the current request
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_URL    => $url,
							CURLOPT_NOBODY => $no_body,
						)
					);

					// Create the response object and get the raw-response
					$this->response = new stdClass;
					$this->response->raw = curl_exec($this->handler);

					// Create the request object
					$this->request = new stdClass;
					$this->request->url       = $url;
					$this->request->size      = curl_getinfo($this->handler, CURLINFO_SIZE_DOWNLOAD);
					$this->request->score     = curl_getinfo($this->handler, CURLINFO_TOTAL_TIME);
					$this->request->redirects = curl_getinfo($this->handler, CURLINFO_REDIRECT_COUNT);

					if ($this->response->raw) {
						// Split the response raw into a headers-body sections (in case of
						// redirects there could be several headers sections)
						$sections = explode(
							"\r\n\r\n",
							$this->response->raw,
							($this->request->redirects + 2)
						);

						// Get the response body
						$this->response->body = array_pop($sections);

						// Parse response headers
						$this->response_headers_parse($sections);

						// Check the user-defined extra-parser for headers and run it
						if (method_exists($this, 'x_headers_parse')) {
							$this->x_headers_parse();
						}

						// Check the user-defined extra-parser for body and run it
						if (method_exists($this, 'x_body_parse')) {
							$this->x_body_parse();
						}

						$this->http_request_unset();

						// Reset CURLOPT_NOBODY to default value
						if ($no_body) {
							curl_setopt($this->handler, CURLOPT_NOBODY, false);
						}

						// Clear raw property of the request object
						if (!$raw_save) {
							unset($this->response->raw);
						}
					}
				}

				return $this;
			}

		/**
		 * Send a GET request
		 *
		 * @public
		 * @method
		 *
		 * @param string  $url
		 * @param boolean $no_body
		 * @param boolean $raw_save
		 *
		 * @return object
		 */
		public function
			get_request_send($url, $no_body = false, $raw_save = false) {
				return $this->http_request_send($url, $no_body, $raw_save);
			}

		/**
		 * Send a POST-request
		 *
		 * Example of $fields:
		 * $fields = array('field' => 'value', 'file' => '@/path/to/file.png');
		 *
		 * @public
		 * @method
		 *
		 * @param string  $url
		 * @param array   $fields
		 * @param boolean $no_body
		 * @param boolean $raw_save
		 *
		 * @return object
		 */
		public function
			post_request_send($url, $fields = array(), $no_body = false, $raw_save = false) {
				if ($this->ready) {
					// Turn the lib_curl into a POST-mode
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_POST          => true,
							CURLOPT_POSTFIELDS    => http_build_query($fields, '', '&'),
						)
					);

					// Make the request
					$this->http_request_send($url, $no_body, $raw_save);
				}
			}

		/**
		 * Parse the http-headers
		 *
		 * @private
		 * @method
		 *
		 * @param array $sections
		 */
		private function
			response_headers_parse($raw) {
				// Count the headers sections
				$requests = $this->request->redirects + 1;

				// Iterate the headers sessions
				for ($pos = 0; $pos < $requests; $pos++) {
					// Create an object to save the session headers
					$this->response->headers[$pos] = new stdClass;
					$this->response->headers[$pos]->http    = array();
					$this->response->headers[$pos]->headers = array();
					$this->response->headers[$pos]->cookies = array();

					// Get the current section headers list
					$headers = explode("\r\n", $raw[$pos]);

					// Get the protocol version, request number and status
					// from the first session header
					list(
						$this->response->headers[$pos]->http['Version'],
						$this->response->headers[$pos]->http['Status'],
						$this->response->headers[$pos]->http['Title']
					) = explode(' ', array_shift($headers), 3);

					// Iterate the headers sections of the iterated session
					foreach ($headers as $header) {
						// Read the name and the value of the header
						list($name, $value) = explode(': ', $header);

						if ($name == 'Set-Cookie') {
							// Cookies got their own parser
							$this->response_cookie_parse($value, $pos);
						} else {
							// Create the name => value pair in the headers array
							$this->response->headers[$pos]->headers[$name] = $value;
						}

						// Content-Type section got its own parser
						$this->response_http_parse($pos);
					}
				}

				// Create an alias for the last header
				$this->response->head = $this->response->headers[$this->request->redirects];
			}

		/**
		 * Parse response content-type section
		 *
		 * @private
		 * @method
		 *
		 * @param integer $pos
		 */
		private function
			response_http_parse($pos) {
				// Encoding and type headers has their own parser
				if ($this->response->headers[$pos]->headers['Content-Type']) {
					// Parse header into a sections
					$type = explode('; ', $this->response->headers[$pos]->headers['Content-Type']);

					// Get the document type
					$this->response->headers[$pos]->http['Type'] = $type[0];

					if($type[1]) {
						// Get the document encoding
						$this->response->headers[$pos]->http['Charset'] = str_replace('charset=', '', $type[1]);
					}
				}
			}

		/**
		 * Parse response cookies
		 *
		 * @private
		 * @method
		 *
		 * @param string  $raw
		 * @param integer $pos
		 */
		private function
			response_cookie_parse($raw, $pos) {
				// Get the cookie params list
				$params = explode('; ', $raw);

				// Get the cookie name and value from the first param
				list($name, $value) = explode('=', array_shift($params));

				// Create an object to save the cookie params
				$this->response->headers[$pos]->cookies[$name] = new stdClass;
				$this->response->headers[$pos]->cookies[$name]->value = $value;

				// Iterate the rest cookie params
				foreach ($params as $item) {
					// Get the name => value pair for a param
					list($param, $value) = explode('=', $item);

					// Save the cookie info
					$this->response->headers[$pos]->cookies[$name]->{$param} = $value;
				}
			}

		/**
		 * Clean tabs, spaces and line skews from the response body
		 *
		 * @public
		 * @method
		 *
		 * @return object
		 */
		public function
			response_body_clean() {
				if ($this->ready && $this->response->body) {
					$this->response->body = preg_replace(
						"/ {2,30}|\n|\r|\t/i",
						'',
						$this->response->body
					);
				}

				return $this;
			}

		/**
		 * Change the response body encoding from $given into $needed
		 *
		 * koi8-r, windows-1251, utf-8 or any the iconv has
		 *
		 * @public
		 * @method
		 *
		 * @param boolean|string $given
		 * @param boolean|string $needed
		 *
		 * @return object
		 */
		public function
			response_body_recode($given = false, $needed = false) {
				if ($this->ready && $this->response->body) {
					// Use iconv to encode the document body
					$this->response->body = iconv(
						($given ? $given : $this->request->http['Charset']),
						($needed ? $needed : $this->common->charset),
						$this->response->body
					);
				}

				return $this;
			}

		/**
		 * Get http response head section
		 *
		 * @public
		 * @method
		 *
		 * @param boolean|string $section
		 * @param boolean|string $part
		 * @param boolean|string $name
		 *
		 * @return boolean|array|object
		 */
		public function
			response_section_get($section = false, $part = false, $name = false) {
				if ($this->response->{$section}) {
					if ($part && $this->response->{$section}->{$part}) {
						if ($name && $this->response->{$section}->{$part}[$name]) {
							// Get header or cookie value
							return $this->response->{$section}->{$part}[$name];
						}

						// Get part of head section
						return $this->response->{$section}->{$part};
					}

					// Get full response section
					return $this->response->{$section};
				}

				return false;
			}

		/**
		 * Make a listing of the chosen object (main object is default)
		 *
		 * @public
		 * @method
		 *
		 * @param boolean $html
		 */
		public function
			dump_get($html = true) {
				if ($html) {
					echo '<pre>';
				}

				print_r($this);

				if ($html) {
					echo '</pre>';
				}
			}


		/**
		 *********************************
		 **                             **
		 **  Short aliases for methods  **
		 **                             **
		 *********************************
		 */


		/**
		 * Alias for http_request_set()
		 *
		 * @public
		 * @method
		 *
		 * @param string       $section
		 * @param string|array $value
		 *
		 * @return object
		 */
		public function
			set($section, $value) {
				return $this->http_request_set($section, $value);
			}

		/**
		 * Alias for get_request_send()
		 *
		 * @public
		 * @method
		 *
		 * @param string  $url
		 * @param boolean $no_body
		 * @param boolean $raw_save
		 *
		 * @return object
		 */
		public function
			get($url, $no_body = false, $raw_save = false) {
				return $this->get_request_send($url, $no_body, $raw_save);
			}

		/**
		 * Alias for post_request_send()
		 *
		 * @param string  $url
		 * @param array   $fields
		 * @param boolean $no_body
		 * @param boolean $raw_save
		 *
		 * @return object
		 */
		public function
			post($url, $params = array(), $no_body = false, $raw_save = false) {
				return $this->post_request_send($url, $params, $no_body, $raw_save);
			}

		/**
		 * Get unparsed response
		 *
		 * @return boolean|string
		 */
		public function
			raw() {
				return $this->response_section_get('raw');
			}

		/**
		 * Get body section of response
		 *
		 * @return boolean|string
		 */
		public function
			body() {
				return $this->response_section_get('body');
			}


		/**
		 * Get head section of response
		 *
		 * @return boolean|object
		 */
		public function
			heads() {
				return $this->response_section_get('headers');
			}

		/**
		 * Get head section of response
		 *
		 * @return boolean|object
		 */
		public function
			head() {
				return $this->response_section_get('head');
			}

		/**
		 * Get http part of response head section
		 *
		 * @return boolean|array
		 */
		public function
			http() {
				return $this->response_section_get('head', 'http');
			}

		/**
		 * Get headers part of response head section
		 *
		 * @return boolean|array
		 */
		public function
			headers() {
				return $this->response_section_get('head', 'headers');
			}

		/**
		 * Get header by name
		 *
		 * @param boolean|string $name
		 *
		 * @return boolean|array
		 */
		public function
			header($name = false) {
				return $this->response_section_get('head', 'headers', $name);
			}

		/**
		 * Get all cookies
		 *
		 * @return boolean|array
		 */
		public function
			cookies() {
				return $this->response_section_get('head', 'cookies');
			}

		/**
		 * Get cookie by name
		 *
		 * @param boolean|string $name
		 *
		 * @return boolean|array
		 */
		public function
			cookie($name = false) {
				return $this->response_section_get('head', 'cookies', $name);
			}

		/**
		 * Change encoding
		 *
		 * @public
		 * @method
		 *
		 * @param boolean|string $given
		 * @param boolean|string $needed
		 *
		 * @return object
		 */
		public function
			charset($given = false, $needed = false) {
				return $this->response_body_recode($given, $needed);
			}

		/**
		 * Clean tabs, spaces and line skews from the response body
		 *
		 * @public
		 * @method
		 *
		 * @return object
		 */
		public function
			trim() {
				return $this->response_body_clean();
			}

	}


?>