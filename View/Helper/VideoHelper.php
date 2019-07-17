<?php
/**
 * CakePHP helper for embedding YouTube, Vimeo and Dailymotion videos.
 *
 * @name       Video Helper
 * @author     Andy Carter (@drmonkeyninja)
 * @author     Emerson Soares (dev.emerson@gmail.com)
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class VideoHelper extends HtmlHelper {

	protected $_apis = array(
		'youtube_image' => '//i.ytimg.com/vi', // Location of youtube images
		'youtube' => 'https://www.youtube.com', // Location of youtube player
		'vimeo' => '//player.vimeo.com/video', // Location of vimeo videos
		'vimeo_info' => 'https://vimeo.com/api/v2/video/', //Location of vimeo info
		'dailymotion' => '//www.dailymotion.com',
		'wistia' => '//fast.wistia.net'
	);

/**
 * Returns an embedded video.
 *
 * @param string $url video URL
 * @param array $settings (optional) parameters for the embedded video
 * @return string
 */
	public function embed($url, $settings = array()) {
		switch ($this->_getVideoSource($url)) {
			case 'youtube':
				return $this->youtube($url, $settings);
			case 'vimeo':
				return $this->vimeo($url, $settings);
			case 'dailymotion':
				return $this->dailymotion($url, $settings);
			case 'wistia':
				return $this->wistia($url, $settings);
			case false:
			default:
				return $this->_notFound(!empty($settings['failSilently']));
		}
	}

/**
 * Handles the response when no video is found
 *
 * @param boolean $failSilently
 * @return string
 */
	protected function _notFound($failSilently = false) {
		if ($failSilently === true) {
			return;
		} else {
			return $this->tag(
				'div',
				__('Sorry, video does not exists'),
				array('class' => 'error')
			);
		}
	}

/**
 * Returns an embedded Youtube video.
 *
 * @param string $url
 * @param array $settings
 * @return string
 */
	public function youtube($url, $settings = array()) {
		$defaultSettings = array(
			'hd' => true,
			'width' => 624,
			'height' => 369,
			'allowfullscreen' => 'true',
			'frameborder' => 0,
			'related' => 0,
			'autoplay' => 0,
			'loop' => 0,
			'enablejsapi' => 0,
			'showinfo' => 1,
			'class' => ''
		);

		$settings = array_merge($defaultSettings, $settings);
		$videoId = $this->_getVideoId($url, 'youtube');

		if (empty($videoId)) {
			return $this->_notFound(!empty($settings['failSilently']));
		}

		$settings['src'] = $this->_apis['youtube'] . '/embed/' . $videoId . '?hd=' . $settings['hd'] . '&rel=' . $settings['related'] . '&autoplay=' . $settings['autoplay'];
		if (empty($settings['showinfo'])) {
			$settings['src'] .= '&showinfo=0';
		}

		$iframeAttributes = [];
		$youtubeEmbedString = '';

		if ($settings['enablejsapi']) {
			//Throw exception if no id is passed as the iframe needs an id for the api to find the player
			if (empty($settings['id'])) {
				throw new Exception('iframe ID is missing. To use the iframe api the iframe will need an ID.');
			}
			$youtubeEmbedString .= $this->script(
				'https://www.youtube.com/iframe_api'
			);

			$settings['src'] .= '&enablejsapi=1';

			//Attach the origin to the src parameters. Used for security
			if (!empty($settings['origin'])) {
				$settings['src'] .= '&origin=' . $settings['origin'];
			}

			//Attach the id to the iframe
			$iframeAttributes['id'] = $settings['id'];
		}

		if ($settings['loop']) {
			//To loop, the loop parameter needs to be set and the playlist (which is just one video)
			$settings['src'] .= '&loop=1';
			$settings['src'] .= '&playlist=' . $videoId;
		}

		$iframeAttributes = array_merge($iframeAttributes, [
			'width' => $settings['width'],
			'height' => $settings['height'],
			'src' => $settings['src'],
			'frameborder' => $settings['frameborder'],
			'allowfullscreen' => $settings['allowfullscreen']
		]);

		$youtubeEmbedString .= $this->tag('iframe', null, $iframeAttributes) . $this->tag('/iframe');

		return $youtubeEmbedString;
	}

/**
 * Returns an embedded Vimeo video.
 *
 * @param string $url
 * @param array $settings
 * @return string
 */
	public function vimeo($url, $settings = array()) {
		$defaultSettings = array
			(
			'width' => 400,
			'height' => 225,
			'show_title' => 1,
			'show_byline' => 1,
			'show_portrait' => 0,
			'color' => '00adef',
			'allowfullscreen' => 1,
			'autoplay' => 1,
			'loop' => 1,
			'frameborder' => 0,
			'background' => 0,
			'class' => ''
		);
		$settings = array_merge($defaultSettings, $settings);

		$videoId = $this->_getVideoId($url, 'vimeo');

		if (empty($videoId)) {
			return $this->_notFound(!empty($settings['failSilently']));
		}

		$settings['src'] = $this->_apis['vimeo'] . '/' . $videoId . '?title=' . $settings['show_title'] . '&amp;byline=' . $settings['show_byline'] . '&amp;portrait=' . $settings['show_portrait'] . '&amp;color=' . $settings['color'] . '&amp;autoplay=' . $settings['autoplay'] . '&amp;loop=' . $settings['loop'] . '&amp;background=' . $settings['background'];
		return $this->tag('iframe', null, array(
					'src' => $settings['src'],
					'width' => $settings['width'],
					'height' => $settings['height'],
					'frameborder' => $settings['frameborder'],
					'webkitAllowFullScreen' => $settings['allowfullscreen'],
					'mozallowfullscreen' => $settings['allowfullscreen'],
					'allowFullScreen' => $settings['allowfullscreen']
				)) . $this->tag('/iframe');
	}

/**
 * Returns an embedded Dailymotion video.
 *
 * @param string $url
 * @param array $settings
 * @return string
 */
	public function dailymotion($url, $settings = array()) {
		$defaultSettings = array(
			'width' => 480,
			'height' => 270,
			'allowfullscreen' => 'true',
			'frameborder' => 0,
			'related' => 0,
			'class' => ''
		);

		$settings = array_merge($defaultSettings, $settings);

		$videoId = $this->_getVideoId($url, 'dailymotion');

		if (empty($videoId)) {
			return $this->_notFound(!empty($settings['failSilently']));
		}

		$settings['src'] = $this->_apis['dailymotion'] . '/embed/video/' . $videoId . '?related=' . $settings['related'];

		return $this->tag('iframe', null, array(
					'src' => $settings['src'],
					'width' => $settings['width'],
					'height' => $settings['height'],
					'frameborder' => $settings['frameborder'],
					'allowfullscreen' => $settings['allowfullscreen'],
					'class' => $settings['class']),
				) . $this->tag('/iframe');
	}

/**
 * Returns an embedded Wistia video.
 *
 * @param string $url
 * @param array $settings
 * @return string
 */
	public function wistia($url, $settings = array()) {
		$defaultSettings = array(
			'width' => 480,
			'height' => 270,
			'allowfullscreen' => 'true',
			'frameborder' => 0,
			'autoplay' => false,
			'controlsVisibleOnLoad' => true,
			'loop' => false,
			'class' => ''
		);

		$settings = array_merge($defaultSettings, $settings);

		$videoId = $this->_getVideoId($url, 'wistia');

		if (empty($videoId)) {
			return $this->_notFound(!empty($settings['failSilently']));
		}

		$settings['src'] = $this->_apis['wistia'] . '/embed/iframe/' . $videoId;

		$params = array();
		if ($settings['autoplay'] === true) {
			$params['autoPlay'] = true;
		}
		if ($settings['controlsVisibleOnLoad'] === false) {
			$params['controlsVisibleOnLoad'] = false;
		}
		if ($settings['loop'] === true) {
			$params['endVideoBehavior'] = 'loop';
		} elseif (!empty($settings['endVideoBehavior'])) {
			$params['endVideoBehavior'] = $settings['endVideoBehavior'];
		}

		if (!empty($params)) {
			$settings['src'] .= '?' . http_build_query($params);
		}

		return $this->tag('iframe', null, array(
				'src' => $settings['src'],
				'width' => $settings['width'],
				'height' => $settings['height'],
				'frameborder' => $settings['frameborder'],
				'allowfullscreen' => $settings['allowfullscreen'],
				'class' => $settings['class']
			)
		) . $this->tag('/iframe');
	}

/**
 * Returns a Video ID
 *
 * @param string $url Video URL
 * @param string $source (optional) either 'youtube' or 'vimeo'
 * @return string
 */
	protected function _getVideoId($url, $source = null) {
		$source = empty($source) ? $this->_getVideoSource($url) : strtolower($source);

		switch ($source) {
			case 'youtube':
				preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
				return (!empty($matches[1]) ? $matches[1] : $url);
			case 'vimeo':
				$path = parse_url($url, PHP_URL_PATH);
				return substr($path, 1);
			case 'dailymotion':
				return strtok(basename($url), '_');
			case 'wistia':
				$path = parse_url($url, PHP_URL_PATH);
				preg_match('|^/medias/([0-9a-z]+)|i', $path, $matches);
				return !empty($matches[1]) ? $matches[1] : null;
		}

		return;
	}

	protected function _getUrlParams($url) {
		$query = parse_url($url, PHP_URL_QUERY);
		$queryParts = strpos($query, '=') ? explode('&', $query) : array();

		$params = array();

		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			$params[$item[0]] = $item[1];
		}

		return $params;
	}

	protected function _getVideoSource($url) {
		$parsedUrl = parse_url($url);
		if (empty($parsedUrl['host'])) {
			return false;
		}
		$host = $parsedUrl['host'];
		if ($this->_isIp($host) === false) {
			if (!empty($host)) {
				$host = $this->_returnDomain($host);
			} else {
				$host = $this->_returnDomain($url);
			}
		}
		$host = explode('.', $host);

		if (is_int(array_search('vimeo', $host))) {
			return 'vimeo';
		} elseif (is_int(array_search('youtu', $host))) {
			return 'youtube';
		} elseif (is_int(array_search('youtube', $host))) {
			return 'youtube';
		} elseif (is_int(array_search('dailymotion', $host))) {
			return 'dailymotion';
		} elseif (is_int(array_search('wistia', $host))) {
			return 'wistia';
		} else {
			return false;
		}
	}

	protected function _isIp($url) {
		//first of all the format of the ip address is matched
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $url)) {
			//now all the intger values are separated
			$parts = explode(".", $url);
			//now we need to check each part can range from 0-255
			foreach ($parts as $ipParts) {
				if (intval($ipParts) > 255 || intval($ipParts) < 0) {
					return false; //if number is not within range of 0-255
				}
			}
			return true;
		} else {
			return false; //if format of ip address doesn't matches
		}
	}

	protected function _returnDomain($domainb) {
		$bits = explode('/', $domainb);
		if ($bits[0] == 'http:' || $bits[0] == 'https:') {
			$domainb = $bits[2];
		} else {
			$domainb = $bits[0];
		}
		unset($bits);
		$bits = explode('.', $domainb);
		$idz = count($bits);
		$idz -= 3;
		if (strlen($bits[($idz + 2)]) == 2) {
			$url = $bits[$idz] . '.' . $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
		} elseif (strlen($bits[($idz + 2)]) == 0) {
			$url = $bits[($idz)] . '.' . $bits[($idz + 1)];
		} else {
			$url = $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
		}
		return $url;
	}

/**
 * Returns an video thumbnail url.
 *
 * @param string $url video URL
 * @param string $size (optional) size of the thumbnail to get.
 * @param array $settings (optional) parameters for the embedded video
 * @return string
 */
	public function thumbnail($url, $size = 'thumb', $settings = array()) {
		switch ($this->_getVideoSource($url)) {
			case 'youtube':
				return $this->youtubeThumbnail($url, $size, $settings);
			case 'vimeo':
				return $this->vimeoThumbnail($url, $size, $settings);
			case false:
			default:
				return $this->_notFound(!empty($settings['failSilently']));
		}
	}

/**
 * Returns a Youtube video image
 *
 * Available images:-
 *
 * 		thumb - 120px x 90px (4:3)
 * 		large - 480px x 360px (4:3)
 * 		thumb1 - 120px x 90px (4:3) taken at 25% through the video
 * 		thumb2 - 120px x 90px (4:3) taken at 50% through the video
 * 		thumb3 - 120px x 90px (4:3) taken at 75% through the video
 * 		wide - 320px x 180px (16:9)
 * 		maxres - large image, not always available
 *
 * @param string $url Youtube video URL
 * @param string $size (optional) thumbnail to be used
 * @param array $options (optional) parameters for HtmlHelper::image()
 * @return string
 */
	public function youtubeThumbnail($url, $size = 'thumb', $options = array()) {
		$videoId = $this->_getVideoId($url);

		$acceptedSizes = array(
			'thumb' => 'default', // 120px x 90px
			'large' => 0, // 480px x 360px
			'thumb1' => 1, // 120px x 90px at position 25%
			'thumb2' => 2, // 120px x 90px at position 50%
			'thumb3' => 3, // 120px x 90px at position 75%
			'wide' => 'mqdefault',
			'maxres' => 'maxresdefault'
		);

		if (! isset($acceptedSizes[$size]) === true) {
			return;
		}

		$imageUrl = $this->_apis['youtube_image'] . '/' . $videoId . '/' . $acceptedSizes[$size] . '.jpg';
		return $this->image($imageUrl, $options);
	}

/**
 * Returns a Vimeo video image
 *
 * Available images:-
 *
 * 		small - 100px x 75px (4:3)
 * 		medium - 200px x 150px (4:3)
 * 		large - 640px x 480px (4:3)
 *
 * @param string $url Vimeo video URL
 * @param string $size (optional) thumbnail to be used
 * @param array $options (optional) parameters for HtmlHelper::image()
 * @return string
 */
	public function vimeoThumbnail($url, $size = 'thumb', $options = array()) {
		$videoId = $this->_getVideoId($url);

		$acceptedSizes = array(
			'thumb' => 'medium',
			'small' => 'small',
			'medium' => 'medium',
			'large' => 'large',
		);

		if (empty($acceptedSizes[$size]) === true) {
			return;
		}

		try {
			$videoInfo = @file_get_contents($this->_apis['vimeo_info'] . $videoId . '.php');
			$videoInfo = unserialize($videoInfo);
		} catch (Exception $e) {
			$videoInfo = null;
		}

		if (empty($videoInfo) === true) {
			return;
		}

		$imageUrl = $videoInfo[0]['thumbnail_' . $acceptedSizes[$size]];
		return $this->image($imageUrl, $options);
	}

}
