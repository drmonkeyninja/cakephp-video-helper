<?php
/**
 * CakePHP helper for embedding YouTube, Vimeo and Dailymotion videos.
 *
 * @name       Video Helper
 * @author     Andy Carter (@drmonkeyninja)
 * @author     Emerson Soares (dev.emerson@gmail.com)
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace VideoEmbed\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;

class VideoHelper extends HtmlHelper
{
    protected $_apis = [
        'youtube_image' => '//i.ytimg.com/vi', // Location of youtube images
        'youtube' => '//www.youtube.com', // Location of youtube player
        'vimeo' => '//player.vimeo.com/video',
        'bbc' => '//www.bbc.co.uk',
        'dailymotion' => '//www.dailymotion.com',
        'wistia' => '//fast.wistia.net'
    ];

    /**
     * Returns an embedded video.
     *
     * @param string $url video URL
     * @param array $settings (optional) parameters for the embedded video
     * @return string
     */
    public function embed($url, $settings = [])
    {
        switch ($this->_getVideoSource($url)) {
            case 'youtube':
                return $this->youtube($url, $settings);
            case 'vimeo':
                return $this->vimeo($url, $settings);
            case 'dailymotion':
                return $this->dailymotion($url, $settings);
            case 'wistia':
                return $this->wistia($url, $settings);
            case 'bbc':
                return $this->bbc($url, $settings);
            case false:
            default:
                return $this->_notFound(!empty($settings['failSilently']));
        }
    }

    /**
     * Handles the response when no video is found
     *
     * @param bool $failSilently Pass true to prevent output of error messages to page
     * @return string
     */
    protected function _notFound($failSilently = false)
    {
        if ($failSilently === true) {
            return '';
        } else {
            return $this->tag(
                'div',
                __('Sorry, video does not exists'),
                ['class' => 'error']
            );
        }
    }

    /**
     * Returns an embedded Youtube video.
     *
     * @param string $url Video URL
     * @param array $settings Video player settings
     * @return string
     */
    public function youtube($url, $settings = [])
    {
        $defaultSettings = [
            'hd' => true,
            'width' => 624,
            'height' => 369,
            'allowfullscreen' => true,
            'frameborder' => 0,
            'related' => false,
            'autoplay' => false,
            'showinfo' => true,
            'mute' => false,
            'enablejsapi' => false,
        ];

        $settings = array_merge($defaultSettings, $settings);
        $videoId = $this->_getVideoId($url, 'youtube');

        if (empty($videoId)) {
            return $this->_notFound(!empty($settings['failSilently']));
        }

        $settings['src'] = $this->_apis['youtube'] . '/embed/' .
            $videoId . '?hd=' . (int)$settings['hd'] . '&rel=' . (int)$settings['related'] . '&autoplay=' . (int)$settings['autoplay'] .
            '&showinfo=' . (int)$settings['showinfo'] . '&mute=' . (int)$settings['mute'] . '&enablejsapi=' . (int)$settings['enablejsapi'];
        $iframeOptions = [
            'width' => $settings['width'],
            'height' => $settings['height'],
            'src' => $settings['src'],
            'frameborder' => (int)$settings['frameborder'],
            'allowfullscreen' => $settings['allowfullscreen'],
            'autoplay' => (int)$settings['autoplay'],

        ];
        if (!empty($settings['allow'])) {
            $iframeOptions['allow'] = $settings['allow'];
        }
        if (!empty($settings['class'])) {
            $iframeOptions['class'] = $settings['class'];
        }

        return $this->tag('iframe', null, $iframeOptions) . $this->tag('/iframe');
    }

    /**
     * Returns an embedded Vimeo video.
     *
     * @param string $url Vimeo URL
     * @param array $settings Video player settings
     * @return string
     */
    public function vimeo($url, $settings = [])
    {
        $defaultSettings = [
            'width' => 400,
            'height' => 225,
            'show_title' => true,
            'show_byline' => true,
            'show_portrait' => false,
            'color' => '00adef',
            'allowfullscreen' => true,
            'autoplay' => true,
            'loop' => true,
            'frameborder' => 0
        ];
        $settings = array_merge($defaultSettings, $settings);

        $videoId = $this->_getVideoId($url, 'vimeo');

        if (empty($videoId)) {
            return $this->_notFound(!empty($settings['failSilently']));
        }

        $settings['src'] = $this->_apis['vimeo'] . '/' . $videoId . '?title=' . (int)$settings['show_title'] . '&byline=' . (int)$settings['show_byline'] . '&portrait=' . (int)$settings['show_portrait'] . '&color=' . $settings['color'] . '&autoplay=' . (int)$settings['autoplay'] . '&loop=' . (int)$settings['loop'];

        return $this->tag('iframe', null, [
                    'src' => $settings['src'],
                    'width' => $settings['width'],
                    'height' => $settings['height'],
                    'frameborder' => (int)$settings['frameborder'],
                    'webkitAllowFullScreen' => (int)$settings['allowfullscreen'],
                    'mozallowfullscreen' => (int)$settings['allowfullscreen'],
                    'allowfullscreen' => $settings['allowfullscreen']
                ]) . $this->tag('/iframe');
    }

    /**
     * Returns an embedded Dailymotion video.
     *
     * @param string $url Daily Motion URL
     * @param array $settings Video player settings
     * @return string
     */
    public function dailymotion($url, $settings = [])
    {
        $defaultSettings = [
            'width' => 480,
            'height' => 270,
            'allowfullscreen' => true,
            'frameborder' => 0,
            'related' => false
        ];

        $settings = array_merge($defaultSettings, $settings);

        $videoId = $this->_getVideoId($url, 'dailymotion');

        if (empty($videoId)) {
            return $this->_notFound(!empty($settings['failSilently']));
        }

        $settings['src'] = $this->_apis['dailymotion'] . '/embed/video/' . $videoId . '?related=' . (int)$settings['related'];

        return $this->tag(
            'iframe',
            null,
            [
                'src' => $settings['src'],
                'width' => $settings['width'],
                'height' => $settings['height'],
                'frameborder' => $settings['frameborder'],
                'allowfullscreen' => $settings['allowfullscreen']
            ]
        ) . $this->tag('/iframe');
    }

    /**
     * Returns an embedded Wistia video.
     *
     * @param string $url Wistia URL
     * @param array $settings Video player settings
     * @return string
     */
    public function wistia($url, $settings = [])
    {
        $defaultSettings = [
            'width' => 480,
            'height' => 270,
            'allowfullscreen' => true,
            'frameborder' => 0
        ];

        $settings = array_merge($defaultSettings, $settings);

        $videoId = $this->_getVideoId($url, 'wistia');

        if (empty($videoId)) {
            return $this->_notFound(!empty($settings['failSilently']));
        }

        $settings['src'] = $this->_apis['wistia'] . '/embed/iframe/' . $videoId;

        return $this->tag(
            'iframe',
            null,
            [
                'src' => $settings['src'],
                'width' => $settings['width'],
                'height' => $settings['height'],
                'frameborder' => $settings['frameborder'],
                'allowfullscreen' => $settings['allowfullscreen']
            ]
        ) . $this->tag('/iframe');
    }

    /**
     * Returns an embedded BBC video.
     *
     * @param string $url BBC URL
     * @param array $settings Video player settings
     * @return string
     */
    public function bbc($url, $settings = [])
    {
        $defaultSettings = [
            'width' => 500,
            'height' => 400,
            'allowfullscreen' => true,
            'frameborder' => 0
        ];

        $settings = array_merge($defaultSettings, $settings);

        $videoId = $this->_getVideoId($url, 'bbc');

        if (empty($videoId)) {
            return $this->_notFound(!empty($settings['failSilently']));
        }

        $settings['src'] = $this->_apis['bbc'] . '/programmes/' . $videoId . '/player';

        return $this->tag(
            'iframe',
            null,
            [
                'src' => $settings['src'],
                'width' => $settings['width'],
                'height' => $settings['height'],
                'frameborder' => $settings['frameborder'],
                'allowfullscreen' => $settings['allowfullscreen']
            ]
        ) . $this->tag('/iframe');
    }

    /**
     * Returns a Video ID
     *
     * @param string $url Video URL
     * @param string $source (optional) either 'youtube' or 'vimeo'
     * @return string
     */
    protected function _getVideoId($url, $source = null)
    {
        $source = empty($source) ? $this->_getVideoSource($url) : strtolower($source);

        switch ($source) {
            case 'youtube':
                $params = $this->_getUrlParams($url);

                return (isset($params['v']) ? $params['v'] : $url);
            case 'vimeo':
                $path = parse_url($url, PHP_URL_PATH);

                return substr($path, 1);
            case 'dailymotion':
                return strtok(basename($url), '_');
            case 'wistia':
                $path = parse_url($url, PHP_URL_PATH);
                preg_match('|^/medias/([0-9a-z]+)|i', $path, $matches);

                return !empty($matches[1]) ? $matches[1] : null;
            case 'bbc':
                $path = parse_url($url, PHP_URL_PATH);
                preg_match('|^/programmes/([0-9a-z]+)|i', $path, $matches);

                return !empty($matches[1]) ? $matches[1] : null;
        }
    }

    /**
     * Returns URL parameters
     *
     * @param  str $url URL
     * @return array
     */
    protected function _getUrlParams($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        $queryParts = strpos($query, '=') ? explode('&', $query) : [];

        $params = [];

        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }

    /**
     * Returns the video source from a URL
     * @param  str $url URL
     * @return str Video source
     */
    protected function _getVideoSource($url)
    {
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
        } elseif (is_int(array_search('youtube', $host))) {
            return 'youtube';
        } elseif (is_int(array_search('dailymotion', $host))) {
            return 'dailymotion';
        } elseif (is_int(array_search('wistia', $host))) {
            return 'wistia';
        } elseif (is_int(array_search('bbc', $host))) {
            return 'bbc';
        } else {
            return false;
        }
    }

    /**
     * Check if IP
     *
     * @param  str $url URL
     * @return bool
     */
    protected function _isIp($url)
    {
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

    /**
     * Returns the domain
     * @param  str $domainb Domain
     * @return str
     */
    protected function _returnDomain($domainb)
    {
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
     * Returns a Youtube video image
     *
     * Available images:-
     *
     *      thumb - 120px x 90px (4:3)
     *      large - 480px x 360px (4:3)
     *      thumb1 - 120px x 90px (4:3) taken at 25% through the video
     *      thumb2 - 120px x 90px (4:3) taken at 50% through the video
     *      thumb3 - 120px x 90px (4:3) taken at 75% through the video
     *      wide - 320px x 180px (16:9)
     *      maxres - large image, not always available
     *
     * @param string $url Youtube video URL
     * @param string $size (optional) thumbnail to be used
     * @param array $options (optional) parameters for HtmlHelper::image()
     * @return string|void
     */
    public function youtubeThumbnail($url, $size = 'thumb', $options = [])
    {
        $videoId = $this->_getVideoId($url);

        $acceptedSizes = [
            'thumb' => 'default', // 120px x 90px
            'large' => 0, // 480px x 360px
            'thumb1' => 1, // 120px x 90px at position 25%
            'thumb2' => 2, // 120px x 90px at position 50%
            'thumb3' => 3, // 120px x 90px at position 75%
            'wide' => 'mqdefault',
            'maxres' => 'maxresdefault'
        ];

        if (! isset($acceptedSizes[$size]) === true) {
            return;
        }

        $imageUrl = $this->_apis['youtube_image'] . '/' . $videoId . '/' . $acceptedSizes[$size] . '.jpg';

        return $this->image($imageUrl, $options);
    }
}
