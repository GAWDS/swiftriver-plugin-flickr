<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Flickr crawler
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @category   Libraries
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Crawler_Flickr {

	/**
	 * Fetch flickr feeds attached to a river id
	 *
	 * @param   int   $river_id	 
	 * @return  bool
	 */
	public function crawl($river_id)
	{
		// If the river ID is NULL or non-existent, exit
		if (empty($river_id) OR ! ORM::factory('river', $river_id)->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Invalid database river id: :river_id', 
				array(':river_id' => $river_id));
			
			return FALSE;
		}

		// If phpFlickr vendor not found, exit
		$path = Kohana::find_file( 'vendor', 'phpFlickr/phpFlickr' );
		if( FALSE === $path )
		{
			Kohana::$log->add(Log::ERROR, 'phpFlickr vendor not found');
			
			return FALSE;
		}
		require_once( $path );

		// Get the keywords and users to search form the db
		$filter_options = Model_Channel_Filter::get_channel_filter_options('flickr', $river_id);


		// Load config
		$config = Kohana::$config->load('flickr');
		$api_key = $config->get('api_key');
		$api_secret = $config->get('api_secret');

		if ( ! $api_key)
		{
			Kohana::$log->add(Log::ERROR, 'SwiftRiver does not have a Flickr API Key. Please sign up for one at http://www.flickr.com/services/apps/create');
		
			return FALSE;
		}

		if ( ! empty($filter_options))
		{
			// Instantiate the API handler object
			$flickr = new phpFlickr($api_key);

			// Its possible to make a single call for all the keywords,
			// So we'll join all of them
			$keywords_array = array();
			foreach ($filter_options as $option)
			{				
				$keywords_array[] = $option['data']['value'];
			}

			$keywords = implode(',', $keywords_array);

			$photos = $flickr->photos_search(
				array(
					'tags'=>$keywords, 
					'tag_mode'=>'any',
					'extras'=>'description,date_upload,date_taken,owner_name,geo,tags,machine_tags'
				)
			);

			if (isset($photos['photo']))
			{
				foreach ($photos['photo'] as $photo)
				{
					// Get the droplet template
					$droplet = Swiftriver_Dropletqueue::get_droplet_template();

					// Populate the droplet
					$droplet['channel'] = 'flickr';
					$droplet['river_id'] = array($river_id);
					$droplet['identity_orig_id'] = $photo['owner'];
					$droplet['identity_username'] = $photo['owner'];
					$droplet['identity_name'] = $photo['ownername'];
					$droplet['identity_avatar'] = '';
					$droplet['droplet_orig_id'] = $photo['id'];
					$droplet['droplet_type'] = 'original';
					$droplet['droplet_title'] = $photo['title'];
					$droplet['droplet_raw'] = $droplet['droplet_content'] = $photo['description'];
					$droplet['droplet_date_pub'] = gmdate("Y-m-d H:i:s", strtotime($photo['datetaken']));
					
					// Get Photo
					$droplet['media'] = array(
									array(
										'url' => $flickr->buildPhotoURL($photo, 'medium'),
										'droplet_image' => $flickr->buildPhotoURL($photo, 'medium'),
										'type' => 'image'
									),
								);

					// Get Flickr Photo Location
					if ($photo['latitude'] != 0 AND $photo['longitude'] != 0)
					{
						$droplet['places'] = array(
										array(
											'place_name' => 'unknown',
											'latitude' => $photo['latitude'],
											'longitude' => $photo['longitude'],
											'source' => 'flickr'
										),
									);
					}

					// Get User Tags
					$droplet['tags'] = array();
					$tags = explode(' ', $photo['tags']);
					foreach ($tags as $tag)
					{
						$droplet['tags'][] = array(
											'tag_name' => $tag,
											'tag_type' => 'flickr'
										);
					}

					Swiftriver_Dropletqueue::add($droplet);	
				}
			}
		}
	}
}