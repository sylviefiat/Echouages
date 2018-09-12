<?php


defined('_JEXEC') or die('Restricted access');

/**
 * 
 * 
 */
class JOFileRetriever
{

        protected $bCurlEnabled     = FALSE;
        protected $bAllowFopenUrl   = FALSE;
        protected $rCh              = NULL;
        protected static $instances = array();
        public $params;

        /**
         * 
         */
        protected function __construct($params = null)
        {
                $this->params = $params;

                if (in_array('curl', get_loaded_extensions()))
                {
                        $this->bCurlEnabled = TRUE;
                }

                if (ini_get('allow_url_fopen'))
                {
                        $this->bAllowFopenUrl = TRUE;
                }
        }

        /**
         * 
         */
        public function __destruct()
        {
                if (!is_null($this->rCh))
                {
                        curl_close($this->rCh);
                }
        }		
		
        /**
         * 
         * @return type
         */
        public static function getInstance($params = null)
        {
                $hash = md5(serialize($params));

                if (!isset(self::$instances[$hash]))
                {
                        self::$instances[$hash] = new JOFileRetriever($params);
                }

                return self::$instances[$hash];
        }
		
        /**
         * 
         * @param type $sPath
         * @return type
         */
        public function getFileContents($sPath, $aPost = array())
        {
			$sPath = self::getFilePath($sPath);
			
			if ((strpos($sPath, 'http') === 0) && $this->bCurlEnabled){
				return $this->getContentsWithCurl($sPath, $aPost);
			} else {
				return $this->getContents($sPath, $aPost);
			}
        }

        /**
         * 
         * @param type $sPath
         * @return type
         * @throws Exception
         */
        protected function getContentsWithCurl($sPath, $aPost = array() )
        {
                $rCh = $this->getCurlResource();

                curl_setopt($rCh, CURLOPT_HEADER, 0);
                curl_setopt($rCh, CURLOPT_URL, $sPath);
                curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($rCh, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, TRUE);
                curl_setopt($rCh, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($rCh, CURLOPT_CAINFO, JPATH_PLUGINS . '/system/joptimizer/rew/cacert.pem');
                @curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, TRUE);

                if (!empty($aPost))
                {
                        curl_setopt($rCh, CURLOPT_POST, TRUE);
                        curl_setopt($rCh, CURLOPT_POSTFIELDS, $aPost);
                }

                $sContents = curl_exec($rCh);

                $sResponseCode = curl_getinfo($rCh, CURLINFO_HTTP_CODE);

                if ($sResponseCode == 404)
                {
                        $sContents = self::notFound($sPath);
                }

                if ($sContents === FALSE || trim($sContents) == '')
                {
				/*
                        JOLogger::log(
                                JText::_(
                                        sprintf(
                                                'Curl failed with error #%d: %s '
                                                . 'fetching contents from %s', curl_errno($rCh), curl_error($rCh), $sPath
                                        )
                                ), $this->params
                        );
				*/
				
                        if ($this->bAllowFopenUrl)
                        {
                                $sContents = $this->getContents($sPath);
                        }
                        else
                        {
                                throw new Exception(curl_error($rCh), curl_errno($rCh));
                        }
                }

                return $sContents;
        }

        /**
         * 
         * @param type $sPath
         * @return type
         */
        protected function getContents($sPath, $aPost = array())
        {
                $context = NULL;
                $bExists = TRUE;

                if (!empty($aPost))
                {
                        $postdata = http_build_query($aPost);

                        $opts = array('http' =>
                                array(
                                        'method'  => 'POST',
                                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                                        'content' => $postdata
                                )
                        );

                        $context = stream_context_create($opts);
                }

                if ((strpos($sPath, 'http') === 0) && $this->bAllowFopenUrl)
                {
                        $aResponseHeaders = @get_headers($sPath);

                        if (strpos($aResponseHeaders[0], '404') !== FALSE)
                        {
                                $bExists = FALSE;
                        }
                }
                else
                {
                        $bExists = file_exists($sPath);
                }

                if ($bExists)
                {
                        $sContents = @file_get_contents($sPath, FALSE, $context);
                }
                else
                {
                        $oUri        = clone JUri::getInstance();
                        $sJbase      = JUri::base(true);
                        $sBaseFolder = $sJbase == '/' ? $sJbase : $sJbase . '/';
                        $sUriPath    = $oUri->toString(array('scheme', 'user', 'pass', 'host', 'port')) . $sBaseFolder .
                                (str_replace(DIRECTORY_SEPARATOR, '/', str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $sPath)));

                        $sContents = $this->getFileContents($sUriPath);

                        if ($sContents == self::notFound($sUriPath))
                        {
                                $sContents = self::notFound($sPath);
                        }
                }

                return $sContents;
        }

        /**
         * 
         * @return type
         */
        public function getCurlResource()
        {
                if (is_null($this->rCh))
                {
                        $this->rCh = curl_init();
                }

                return $this->rCh;
        }

        /**
         * 
         * @return type
         */
        public function isUrlFOpenAllowed()
        {
                return ($this->bAllowFopenUrl || $this->bCurlEnabled);
        }

        /**
         * 
         * @param type $sPath
         * @return type
         */
        public static function notFound($sPath)
        {
                return '/* "File [' . $sPath . '] not found" */';
        }

	/**
	 * Get local path of file from the url if internal
	 * If external or php file, the url is returned
	 *
	 * @param string  $url  Url of file
	 * @return string       File path
	 */
	public static function getFilePath($url)
	{
		$uriBase = str_replace('/administrator/', '', JUri::base());
		$uriPath = str_replace('/administrator', '', JUri::base(true));

		$jUri = clone JUri::getInstance($uriBase);

		$parsedUrl = parse_url($url);

		if (self::isInternal($url) && preg_match('#\.(?:css|js|png|jpg|gif|jpeg)$#i', $parsedUrl['path'])){
			$url = preg_replace(
					array(
					'#^' . preg_quote($uriBase, '#') . '#',
					'#^' . preg_quote($uriPath, '#') . '/#',
					'#\?.*?$#'
					), '', $url);

			$result = JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $url);
		} else {
			switch (true){
				case preg_match('#://#', $url):
					break;

				case (substr($url, 0, 2) == '//'):
					$url = $jUri->toString(array('scheme')) . substr($url, 2);
					break;

				case (substr($url, 0, 1) == '/'):
					$url = $jUri->toString(array('scheme', 'host')) . $url;
					break;

				default:
					$url = $uriBase . $url;
					break;
			}
			
			$result = html_entity_decode($url);
		}
		
		return $result;
	}

	/**
	 * Determines if file is internal
	 * 
	 * @param string $url  Url of file
	 * @return boolean
	 */
	public static function isInternal($url){
		$jUri = JUri::getInstance($url);
		//trying to resolve bug in php with parse_url before 5.4.7
		if (preg_match('#^//([^/]+)(/.*)$#i', $jUri->getPath(), $matches) AND !empty($matches)){
			$jUri->setHost($matches[1]);
			$jUri->setPath($matches[2]);
		}

		$urlBase = $jUri->toString(array('scheme', 'host', 'port', 'path'));
		$urlHost = $jUri->toString(array('scheme', 'host', 'port'));

		$base = str_replace('administrator/', '', JUri::base());

		if (stripos($urlBase, $base) !== 0 && !empty($urlHost)){
			return false;
		}

		return true;
	}		
		
}

//copied from http://slopjong.de/2012/03/31/curl-follow-locations-with-safe_mode-enabled-or-open_basedir-set/
function curl_exec_follow($ch, &$maxredirect = null) {
  
  // we emulate a browser here since some websites detect
  // us as a bot and don't let us do our job
  $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5)".
                " Gecko/20041107 Firefox/1.0";
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent );

  $mr = $maxredirect === null ? 5 : intval($maxredirect);

  if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  } else {
    
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if ($mr > 0)
    {
      $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
      $newurl = $original_url;
      
      $rch = curl_copy_handle($ch);
      
      curl_setopt($rch, CURLOPT_HEADER, true);
      curl_setopt($rch, CURLOPT_NOBODY, true);
      curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
      do
      {
        curl_setopt($rch, CURLOPT_URL, $newurl);
        $header = curl_exec($rch);
        if (curl_errno($rch)) {
          $code = 0;
        } else {
          $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
          if ($code == 301 || $code == 302) {
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $newurl = trim(array_pop($matches));
            
            // if no scheme is present then the new url is a
            // relative path and thus needs some extra care
            if(!preg_match("/^https?:/i", $newurl)){
              $newurl = $original_url . $newurl;
            }   
          } else {
            $code = 0;
          }
        }
      } while ($code && --$mr);
      
      curl_close($rch);
      
      if (!$mr)
      {
        if ($maxredirect === null){
        	trigger_error('Too many redirects.', E_USER_WARNING);
	} else {
		$maxredirect = 0;
        }
	
        return false;
      }
      curl_setopt($ch, CURLOPT_URL, $newurl);
    }
  }
  return curl_exec($ch);
}
