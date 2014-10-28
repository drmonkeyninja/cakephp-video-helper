VideoEmbed
==========

This plugin provides a Video helper that generates the tags for embedding videos from Youtube, Vimeo, Dailymotion and Wistia.


Installation
------------

This plugin can be installed using Composer:-

    composer require drmonkeyninja/cakephp-video-helper

Alternatively copy the plugin to your app/Plugin directory and rename the plugin's directory 'VideoEmbed'.

Then add the following line to your bootstrap.php to load the plugin.

    CakePlugin::load('VideoEmbed');


Usage
-----

    echo $this->Video->embed($video['Video']['url'], array(
        'width' => 450,
        'height' => 300,
        'failSilently' => true // Disables warning text when URL is not recognised
    ));
    
    // Advanced usage
    echo $this->Video->embed($video['Video']['url'], array(
        'width' => 450,
        'height' => 300,
    	'allowfullscreen' => 1,
    	'loop' => 1,
    	'color' => '00adef',
    	'show_title' => 1,
    	'show_byline' => 1,
    	'show_portrait' => 0,
    	'autoplay' => 1,
    	'frameborder' => 0
    ));

Some of these settings are applicable only to Vimeo if the video is on Youtube, Dailymotion or Wistia they are ignored.
