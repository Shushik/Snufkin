<?


	/**
	 * Snufkin
	 *
	 * OOP-Libcurl extention
	 *
	 *
	 * System requirements:
	 *   – PHP ≥ 5;
	 *   – libcurl;
	 *   – iconv.
	 *
	 *
	 * Code example:
	 *   $Snufkin = new Snufkin(array(
	 *      'timeout'   => 5,
	 *      'redirects' => 10,
	 *      'agent'     => 'win.ff',
	 *      'referer'   => 'http://www.yandex.ru/',
	 *      'cookies'   => '/path/to/cookies.jar',
	 *      'charset'   => 'utf-8',
	 *     'encoding'  => 'gzip,deflate',
	 *   ));
	 *
	 *   $Snufkin->request_send('http://google.com');
	 *
	 *
	 * @author  Shushik <silkleopard@yandex.ru>
	 * @version 3.2
	 */
	class Snufkin {

		public
			$ready    = false,
			$request  = null,
			$response = null;


		private
			$agents  = array(
				'win.ie.5'   => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; SV1)',
				'win.ie.6'   => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)',
				'win.ie.7'   => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
				'win.ie.8'   => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
				'win.ff'     => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6',
				'win.opera'  => 'Opera/9.63 (Windows NT 5.1; U; en)',
				'win.safari' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Version/3.1.2 Safari/525.21',
				'win.chrome' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.48 Safari/525.19',

				'lin.ff'     => 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1b3pre) Gecko/20090217 Shiretoko/3.1b3pre',
				'lin.kq'     => 'Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.10 (like Gecko)',
				'lin.opera'  => 'Opera/9.63 (X11; Linux i686; U; en) Presto/2.1.1',

				'bsd.lynx'   => 'Lynx/2.8.6rel.5 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.7e-p1',
				'bsd.links'  => 'Links (2.2; FreeBSD 7.0-RC1 i386; 195x65)',

				'mac.ff'     => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.6) Gecko/2009011912 Firefox/3.0.6',
				'mac.opera'  => 'Opera/9.62 (Macintosh; Intel Mac OS X; U; en) Presto/2.1.1',
				'mac.safari' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1',
			),
			$common  = null,
			$handler = null;


		/**
		 * Create a link to lib_curl, set up basic lib_curl settings
		 *
		 * @constructor
		 * @method
		 *
		 * @param array $conf
		 */
		function
			__construct($conf) {
				// Initiate the connection to the curl
				$this->handler = curl_init();

				if (is_resource($this->handler)) {
					// Turn the ready state identifyer to «on»
					$this->ready = true;

					// Create a common properties object
					$this->common = new stdClass;
					$this->common->charset  = $conf['charset'] ? $conf['charset'] : 'utf-8';
					$this->common->referer  = $conf['referer'] ? $conf['referer'] : 'http://google.com/';
					$this->common->headers  = $conf['headers'] ? $conf['headers'] : array();

					// Set the useragent
					$agent = $conf['agent'] && $this->agents[$conf['agent']] ?
					         $this->agents[$conf['agent']] :
					         'Snufkin Curl Client';

					// Set the maximum redirects limit
					$redirects = $conf['redirects'] ? $conf['redirects'] : 2;

					// Set up the basic curl_lib settings
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_HEADER            => true,
							CURLOPT_TIMEOUT           => $conf['timeout'],
							CURLOPT_ENCODING          => $conf['encoding'],
							CURLOPT_USERAGENT         => $agent,
							CURLOPT_MAXREDIRS         => $redirects,
							CURLOPT_AUTOREFERER       => true,
							CURLOPT_RETURNTRANSFER    => true,
							CURLOPT_FOLLOWLOCATION    => true,
							CURLOPT_UNRESTRICTED_AUTH => true
						)
					);

					// Turn on the curl_lib cookies if the jar-file link given
					if ($conf['cookies']) {
						curl_setopt_array(
							$this->handler,
							array(
								CURLOPT_COOKIEJAR  => $conf['cookies'],
								CURLOPT_COOKIEFILE => $conf['cookies']
							)
						);
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
		 * Send a http-request and make the first parsing of the http-response
		 *
		 * @public
		 * @method
		 *
		 * @param string         $url
		 * @param boolean        $nobody
		 * @param boolean|array  $headers
		 * @param boolean|string $referer
		 * @param boolean        $raw_save
		 */
		public function
			request_send($url, $nobody = false, $headers = false, $referer = false, $raw_save = false) {
				if ($this->ready) {
					// Set up curl_lib settings for the current request
					curl_setopt_array(
						$this->handler,
						array(
							CURLOPT_URL        => $url,
							CURLOPT_NOBODY     => $nobody,
							CURLOPT_REFERER    => ($referer ? $referer : $this->common->referer),
							CURLOPT_HTTPHEADER => ($headers ? $headers : $this->common->headers)
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

					// Get a host name from the request
					$this->request->host = explode('/', $url, 4);
					$this->request->host = implode('/', array_slice($this->request->host, 0, 3));

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
						$this->response_headers_get($sections);

						// Reset CURLOPT_NOBODY to default value
						if ($nobody) {
							curl_setopt($this->handler, CURLOPT_NOBODY, false);
						}

						// Clear raw property of the request object
						if (!$raw_save) {
							unset($this->response->raw);
						}
					}
				} else {
					// Display a lib_curl error
					$this->request->error = curl_error($this->handler);
				}
			}

		/**
		 * Send a GET-request (alias for request_send())
		 *
		 * @public
		 * @method
		 *
		 * @param string         $url
		 * @param boolean        $nobody
		 * @param boolean|array  $headers
		 * @param boolean|string $referer
		 * @param boolean        $raw_save
		 */
		public function
			get_request_send($url, $nobody = false, $headers = false, $referer = false, $raw_save = false) {
				// Call the main function
				$this->request_send($url, $nobody, $headers, $referer, $raw_save);
			}

		/**
		 * Send a POST-request (advanced alias for request_send())
		 *
		 * Example of $fields:
		 *   $fields = array('field' => 'value', 'file' => '@/path/to/file.png');
		 *
		 * @public
		 * @method
		 *
		 * @param string         $url
		 * @param boolean|array  $fields
		 * @param boolean        $nobody
		 * @param boolean|array  $headers
		 * @param boolean|string $referer
		 * @param boolean        $raw_save
		 */
		public function
			post_request_send(
			$url, $fields = false, $nobody = false, $headers = false, $referer = false, $raw_save = false) {
				if ($this->ready) {
					if ($fields) {
						// Set POST request fields
						curl_setopt(
							$this->handler,
							CURLOPT_POSTFIELDS,
							http_build_query($fields, '', '&')
						);
					}

					// Turn the lib_curl into a POST-mode
					curl_setopt($this->handler, CURLOPT_POST, true);

					// Call the main function
					$this->request_send($url, $nobody, $headers, $referer, $raw_save);

					// Turn the lib_curl back into a GET-mode
					curl_setopt($this->handler, CURLOPT_HTTPGET, true);
				} else {
					// Display a lib_curl error
					$this->request->error = curl_error($this->handler);
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
			response_headers_get($sections) {
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
					$headers = explode("\r\n", $sections[$pos]);

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
							$this->response_cookie_get($value, $pos);
						} else {
							// Create the name => value pair in the headers array
							$this->response->headers[$pos]->headers[$name] = $value;
						}

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
				}

				// Create an alias for the last header
				$this->response->head = $this->response->headers[$this->request->redirects];
			}

		/**
		 * Parse response cookies
		 *
		 * @private
		 * @method
		 *
		 * @param string  $src
		 * @param integer $pos
		 */
		private function
			response_cookie_get($src, $pos) {
				// Get the cookie params list
				$params = explode('; ', $src);

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
		 * Change the response body encoding from $given into $needed
		 *
		 * koi8-r, windows-1251, utf-8 or any the iconv has
		 *
		 * @public
		 * @method
		 *
		 * @param string $given
		 * @param string $needed
		 */
		public function
			response_charset_change($given = false, $needed = false) {
				if ($this->ready && $this->response->body) {
					// Use iconv to encode the document body
					$this->response->body = iconv(
						($given ? $given : $this->request->http['Charset']),
						($needed ? $needed : $this->common->charset),
						$this->request->body
					);
				}
			}

		/**
		 * Clean tabs, spaces and line skews from the response body
		 *
		 * @public
		 * @method
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
			}

		/**
		 * Clear the request object
		 *
		 * @public
		 * @method
		 */
		public function
			request_clear() {
				$this->request = null;
			}

		/**
		 * Clear the response object
		 *
		 * @public
		 * @method
		 */
		public function
			response_clear() {
				$this->response = null;
			}

		/**
		 * Clear both request and response objects
		 *
		 * @public
		 * @method
		 */
		public function
			results_clear() {
				$this->request_clear();
				$this->response_clear();
			}

		/**
		 * Make a listing of the chosen object (main object is default)
		 *
		 * @public
		 * @method
		 *
		 * @param boolean|integer|string|array|object $target
		 * @param boolean                             $html
		 */
		public function
			dump_get($target = false, $html = false) {
				if ($html) {
					echo '<pre>';
				}

				print_r($target ? $target : $this);

				if ($html) {
					echo '</pre>';
				}
			}

	}


?>