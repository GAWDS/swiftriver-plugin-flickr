<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Flickr Plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Configs
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

return array(
	
	/**
	 * API Key
	 *
	 * This is required for ALL API calls.
	 * You can yours from http://www.flickr.com/services/api/key.gne
	 */
	'api_key'      => '',


	/**
	 * API Secret
	 *
	 * This is required for all private API calls.
	 * Any time you want to request private information, you must authenticate and the
	 * secret is a required part of making the authentication.
	 * It is NOT needed for public requests.
	 * This can also be found at http://www.flickr.com/services/api/key.gne
	 */
	'api_secret'   => ''
);