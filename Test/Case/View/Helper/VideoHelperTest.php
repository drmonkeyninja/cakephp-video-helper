<?php
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('HtmlHelper', 'View/Helper');
App::uses('VideoHelper', 'VideoEmbed.View/Helper');

class VideoHelperTest extends CakeTestCase {

	public $Video = null;

	public function setUp() {
		parent::setUp();
		$Controller = new Controller();
		$View = new View($Controller);
		$this->Video = new VideoHelper($View);
	}

	public function testEmbed() {

		// Test incorrect video URL.
		$expected = '<div class="error">Sorry, video does not exists</div>';
		$this->assertEquals($expected, $this->Video->embed('http://example.com'));

		$expected = '';
		$this->assertEquals($expected, $this->Video->embed('http://example.com', array('failSilently' => true)));

		// Test embedding a Youtube video.
		$expected = '<iframe width="624" height="369" src="//www.youtube.com/embed/heNGFmEQVq0?hd=1&amp;rel=0" frameborder="0" allowfullscreen="true"></iframe>';
		$this->assertEquals($expected, $this->Video->embed('https://www.youtube.com/watch?v=heNGFmEQVq0'));

		// Test embedding a Vimeo video.
		$expected = '<iframe src="//player.vimeo.com/video/62085792?title=1&amp;amp;byline=1&amp;amp;portrait=0&amp;amp;color=00adef&amp;amp;autoplay=1&amp;amp;loop=1" width="400" height="225" frameborder="0" webkitAllowFullScreen="1" mozallowfullscreen="1" allowFullScreen="1"></iframe>';
		$this->assertEquals($expected, $this->Video->embed('https://vimeo.com/62085792'));

		// Test embedding a Vimeo video.
		$expected = '<iframe src="//www.dailymotion.com/embed/video/x1b6849?related=0" width="480" height="270" frameborder="0" allowfullscreen="true"></iframe>';
		$this->assertEquals($expected, $this->Video->embed('http://www.dailymotion.com/video/x1b6849_baby-panda-sneezing_animals'));

		// Test embedding a Wistia video.
		$expected = '<iframe src="//fast.wistia.net/embed/iframe/1voyrefhy9" width="480" height="270" frameborder="0" allowfullscreen="true"></iframe>';
		$this->assertEquals($expected, $this->Video->embed('https://numed.wistia.com/medias/1voyrefhy9'));

	}


	public function testYouTubeThumbnail() {

		// Test embedding Youtube thumbnail.
		$expected = '<img src="//i.ytimg.com/vi/heNGFmEQVq0/default.jpg" alt="" />';
		$this->assertEquals($expected, $this->Video->youtubeThumbnail('https://www.youtube.com/watch?v=heNGFmEQVq0'));

		// Test embedding a wide Youtube thumbnail.
		$expected = '<img src="//i.ytimg.com/vi/heNGFmEQVq0/mqdefault.jpg" alt="" />';
		$this->assertEquals($expected, $this->Video->youtubeThumbnail('https://www.youtube.com/watch?v=heNGFmEQVq0', 'wide'));

		// Test passing an unsupported thumbnail size.
		$expected = '';
		$this->assertEquals($expected, $this->Video->youtubeThumbnail('https://www.youtube.com/watch?v=heNGFmEQVq0', 'small'));

	}

}