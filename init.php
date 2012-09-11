<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the Flickr plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */

class Flickr_Init {

	public function __construct() 
	{
		// Register a crawler
		Swiftriver_Crawlers::register('flickr', array(new Swiftriver_Crawler_Flickr(), 'crawl'));
	}
}
new Flickr_Init;