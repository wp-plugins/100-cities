<?php
	/*
		Plugin Name: 100Cities
		Plugin URI: http://www.knok.com/
		Text Domain: onehundredcities
		Description: a plugin to show the one of the 100 cities to home swap before you die
		Version: 1.0
		Author: Jonay Pelluz
		Author URI: http://www.jonaypelluz.com
		License: GPL2

		Copyright 2015 Jonay Pelluz  (email : jonaypelluz@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as 
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
	
	if (!function_exists('is_admin')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
	}
	
	//Textdomain for the translation
	$textdomain = 'onehundredcities';
	
	//Load translation
	load_plugin_textdomain($textdomain, false, dirname( plugin_basename(__FILE__) ) . '/lang');

	//Pre Wordpress - 2.6 compatibility
	if (!defined('WP_CONTENT_URL')){
		define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );
	}
	if (!defined('WP_CONTENT_DIR')){
		define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
	}
	
	if(!class_exists("OneHundredCities")){

		class OneHundredCities {
			protected $options_page,$plugin_path,$plugin_url;
			protected $data; //We get options store data, we control this data from the admin page;
			protected $default_lang = "eng"; //You default language, if it isn't set by the widget or include the plugin uses this one
			protected $default_panoramio_photos = 3; //Default quantity of panoramio photos to get
			protected $active_tag = null;
			protected $active_category = null;
			
			//Change template here if you want to use your own
			protected $basic_template = '100Cities_basic.php';
			
			//Change the size of the map depending on the size of the container, and zoom
			protected $map_width = '350';
			protected $map_zoom = 8;
			
			//TODO: Allow all default variables to be passed as array.
			
			function __construct(){	
				
				$this->plugin_path = dirname(__FILE__);
				$this->plugin_url = WP_PLUGIN_URL . '/100-cities';
				
				if(is_admin()){
					if(!class_exists("OneHundredCities_Options")) {
						require($this->plugin_path . '/100Cities-options.php');
					}
					$this->options_page = new OneHundredCities_Options();
				}
				
				//Get save plugin options data
				$this->data = json_decode(get_option('one-hundred-cities-data'));
				
				//Check if tag page or category page options are active, and insert plugin
				if($this->data->tags == 1 || $this->data->categories == 1){
					add_action( 'pre_get_posts', array($this, 'check_page_type'));
				}
				
				//Add plugin template if shortcode is found in post
				add_shortcode( 'onehundredcities', array($this, 'insert_cities_template') );
				
				//Loading custom CSS
				wp_register_style('google_font', 'http://fonts.googleapis.com/css?family=Archivo+Narrow');
				wp_enqueue_style('google_font');
				if($this->data->css != 1){
					wp_enqueue_style('widgets_init', $this->plugin_url . '/assets/100Cities.css');
				} else {
					wp_enqueue_style('widgets_init', $this->plugin_url . '/assets/default-100Cities.css');
				}
				
			}
			
			function check_page_type( $query ){
				//Check page type
				if($this->data->tags == 1 && $query->is_tag()){
					$this->active_tag = $query->query_vars['tag'];
					add_action('wp_meta', array($this, 'insert_plugin_if_tag'));
				}
				if($this->data->categories == 1 && $query->is_category()){
					$this->active_category = $query->query_vars['category'];
				}
			}
			
			function insert_plugin_if_tag(){
				echo $this->insert_cities_template( array( 'location' => $this->active_tag ) );
			}
			
			function check_rss_exists( $url ){ 
				$file_headers = @get_headers($url);
				if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
					return false;
				} else {
					return true;
				}
			}
			
			//removes equal array values
			function custom_array_unique($array, $keep_key_assoc = false) {
				$duplicate_keys = array();
				$tmp = array();

				foreach ($array as $key=>$val) {
					//Convert objects to arrays, in_array() does not support objects
					if (is_object($val))
						$val = (array)$val;

					if (!in_array($val, $tmp))
						$tmp[] = $val;
					else
						$duplicate_keys[] = $key;
				}

				foreach ($duplicate_keys as $key)
					unset($array[$key]);

				return $keep_key_assoc ? $array : array_values($array);
			}
			
			function get_photos_panoramio( $geo, $location, $count ){
				if($count == false){
					$count = $this->default_panoramio_photos;
				}
				$file_name = "panoramio_" . $count . "_" . strtolower(str_replace(" ","-",$location)) . ".log";
				$data = $this->get_cache_data($file_name);
				if(!$data){
					if(!empty($geo)){
						$radius = 0.045;	
						$geoLat = $geo['lat'];	
						$geoLng = $geo['lng'];	
						$panoramio_url = "http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=" . $count . "&minx=" . ( $geoLng - $radius ) . "&miny=" . ( $geoLat - $radius ) . "&maxx=" . ( $geoLng + $radius ) . "&maxy=" . ( $geoLat + $radius ) . "&size=small&mapfilter=true";
						$panoramio = json_decode(file_get_contents($panoramio_url), true);
						$data = $panoramio['photos'];
					}
					if(!empty($data)){
						$this->write_cache($data, $file_name);
					}
				}
				return $data;
			}

			//Get related posts for a given location
			function get_data_by_location( $location, $lng, $custom_feed ){
				$file_name = "articles_" . strtolower(str_replace(" ","-",$location)) . "_" . $lng . ".log";
				$data = $this->get_cache_data($file_name);
				if(!$data){
					$url = $custom_feed;
					if($this->check_rss_exists($url)){
						$doc = new DOMDocument();
						$doc->load($url);
						if($doc->getElementsByTagName('item')){
							$i = 0;
							foreach ($doc->getElementsByTagName('item') as $node) {
								$data['title'] =  $node->getElementsByTagName('title')->item(0)->nodeValue;
								$data['link'] =  strtok($node->getElementsByTagName('link')->item(0)->nodeValue, "?");
								$data['pubDate'] =  date("M, d Y", strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue));
								$data['description'] =  utf8_decode(htmlentities(strip_tags($node->getElementsByTagName('description')->item(0)->nodeValue)));
								$data['description'] =  preg_replace('/&a?m?p?;?#8217;/', "'", $data['description']);
								break;
							}
							if(!empty($data)){
								$this->write_cache($data, $file_name);
							}
						}
					}
				}
				return $data;
			}
			
			function get_cache_data($file_name, $time = false) {
				if(!$time){ $time = 60*60*24*2; }
				$file_url = $this->plugin_path . "/cache/" . $file_name;
				if (file_exists($file_url) && ((time()-filemtime($file_url)) < $time)){
					$str = file_get_contents($file_url);
					return unserialize($str);
				}
			}
			
			function write_cache($data, $file_name) {
				$file_url = $this->plugin_path . "/cache/" . $file_name;
				$fp = fopen($file_url, 'w');
				fwrite($fp, serialize($data));
				fclose($fp);
			}
			
			function insert_cities_template( $atts ) {
				if(!isset($atts['div'])){
					$atts['div'] = $this->data->div;
				}
				if($atts['div'] == 'block'){
					$atts['map_width'] = '900';
				}
				if(isset($atts['wiki']) && $atts['wiki'] == 'off'){ 
					$atts['wiki'] = 0; //Zero means, It won't get printed
				} else {
					$atts['wiki'] = $this->data->wikipedia;
				}
				if(isset($atts['gmaps']) && $atts['gmaps'] == 'off'){ 
					$atts['gmaps'] = 0;
				} else {
					$atts['gmaps'] = $this->data->gmaps;
				}
				if(isset($atts['panoramio']) && $atts['panoramio'] == 'off'){ 
					$atts['panoramio'] = 0;
				} else {
					$atts['panoramio'] = $this->data->panoramio;
				}
				if(isset($atts['offers']) && $atts['offers'] == 'off'){ 
					$atts['offers'] = 0;
				} else {
					$atts['offers'] = $this->data->offers;
				}
				if(isset($atts['logo']) && $atts['logo'] == 'off'){ 
					$atts['logo'] = 0;
				} else {
					$atts['logo'] = $this->data->logo;
				}
				if(!isset($atts['weather']) && $atts['weather'] == 'off'){
					$atts['weather'] = 0;
				} else {
					$atts['weather'] = $this->data->weather;
				}
				if(!isset($atts['mapzoom'])){
					$atts['mapzoom'] = $this->map_zoom;
				}
				if(!isset($atts['panoramiocount']) || $atts['panoramiocount'] == 0 || $atts['panoramiocount'] == ""){
					$atts['panoramiocount'] = false;
				}
				if(!isset($atts['lang'])){
					$atts['lang'] = $this->default_lang; //Default lang is English
				}
				if(isset($atts['location'])){
					return $this->get_city_info( $atts );
				} else {
					return "";
				}
			}
			
			function get_geo_location($location){
				$geo_file_name = "geo_" . strtolower(str_replace(" ","-",$location)) . ".log";
				$data = $this->get_cache_data($geo_file_name);
				if(empty($data)){
					$data = array();
					$gmap_url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($location) . "&sensor=false";
					$json = json_decode(file_get_contents($gmap_url), true);
					if(isset($json['results'][0]['geometry']['location']['lat'])){
						$data['lat'] = $json['results'][0]['geometry']['location']['lat'];
						$data['lng'] = $json['results'][0]['geometry']['location']['lng'];
						$this->write_cache($data, $geo_file_name);
					}
				}
				return $data;
			}
			
			function get_city_info( $params ){
				$data['geo'] = $this->get_geo_location( $params['location'] );
				$data['location'] = $params['location'];				
				$data['div'] = $params['div'];
				if($params['wiki'] == 1){
					$data['wiki'] = $this->get_wikipedia_info( $params['lang'], $params['location'] );
				}
				if($params['gmaps'] == 1){
					if(isset($params['map_width'])){
						$data['map_width'] = $params['map_width'];
					} else {
						$data['map_width'] = $this->map_width;
					}
					$data['zoom'] = $params['mapzoom'];
				}
				if($params['offers'] == 1){
					$file_url = $this->plugin_path . "/assets/offers.data";
					if (file_exists($file_url)){
						$offers = json_decode(file_get_contents($file_url));
					}
					foreach($offers as $offer){
						if($offer->city == str_replace(array('_','-'), array(' ',' '), $params['location'])){
							$offer_data = $offer;
							break;
						}
					}
					if(isset($offer_data) && !empty($offer_data)){
						$data['offers_url'] = $offer_data->link;
						$data['offers_more'] = $offer_data->title . " &raquo;";
						$data['offers_title'] = __("Offers","onehundredcities");
					}
				}
				if($params['panoramio'] == 1 && !empty($data['geo'])){
					$data['panoramio_photos'] = $this->get_photos_panoramio( $data['geo'], $params['location'], $params['panoramiocount']);
				}
				if($params['logo'] == 1){
					$data['logo'] = $this->get_own_info();
				}
				if($params['weather'] == 1){
					$data['weather'] = $this->get_weather($data['geo'], $params['location']);
				}
				if(!empty($data)){
					$template = $this->load_template( $this->plugin_path . '/templates/' . $this->basic_template, $data );
					return $template;
				}
				return false;
			}
			
			function get_weather($geo, $location){
				$file_name = "weather_" . strtolower(str_replace(" ","-",$location)) . ".log";
				$time = 60*60*24;
				$data = $this->get_cache_data($file_name, $time);
				if(empty($data) && !empty($geo)){
					$data = array();
					$url = "http://api.openweathermap.org/data/2.5/forecast/daily?lat=" . $geo['lat'] . "&lon=" . $geo['lng'];
					$params = json_decode($this->curl_url($url));
					$data['weather-desc'] = $params->list[0]->weather[0]->description;
					$data['wind-speed'] = $this->ms_to_kmh(intval($params->list[0]->speed)) . " km/h";
					$data['humidity'] = $params->list[0]->humidity . "% Humidity";
					$data['temperature'] = $this->kelvin_to_celsius(intval($params->list[0]->temp->day)) . "&deg;C";
					$this->write_cache($data, $file_name);
				}
				return $data;
			}
			
			function ms_to_kmh( $ms ){
				return $ms*3.6;
			}
			
			function get_own_info(){
				//Default for all
				$data['title'] = __("Home exchange","onehundredcities") . " | Knok";
				$data['link'] = __("Home-link","onehundredcities");
				$data['img'] = $this->plugin_url . "/assets/logo-knok.png";
				return $data;
			}
			
			function kelvin_to_celsius( $kelvin ){
				return $kelvin - 273.15;
			}
			
			function install(){
				$data['gmaps'] = 1;
				$data['wikipedia'] = 1;
				$data['panoramio'] = 1;
				$data['offers'] = 1;
				$data['tags'] = 0;
				$data['categories'] = 0;
				$data['logo'] = 1;
				$data['div'] = 'float';
				$data['css'] = 1;
				$data['weather'] = 1;
				update_option('one-hundred-cities-data', json_encode($data));
			}
			
			function load_template( $file, $params ) {
				if (is_file($file)) {
					ob_start();
					extract($params);
					include $file;
					return ob_get_clean();
				}
				return false;
			}
			
			function curl_url($url){
				$data = false;
				/*if(function_exists('curl_init')){
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_HTTPGET, true);
					curl_setopt($ch, CURLOPT_POST, false);
					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_NOBODY, false);
					curl_setopt($ch, CURLOPT_VERBOSE, false);
					curl_setopt($ch, CURLOPT_REFERER, "");
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");
					$data = curl_exec($ch);
				*/
				//} else {
					$data = file_get_contents($url);
				//}
				return $data;
			}
			
			function get_wikipedia_info( $lng, $location ) {
				$file_name = "wiki_" . str_replace(" ","-",$location) . "_" . $lng . ".log";
				//$data = $this->get_cache_data($file_name);
				if(!$data){
					$lng_array = array( 
						"eng" => "en", 
						"esp" => "es" 
					);
					$_url = "http://" . $lng_array[$lng] . ".wikipedia.org/w/api.php?action=query&indexpageids=&prop=revisions&titles=" . urlencode($location) ."&rvprop=content&format=json";
					$page = json_decode($this->curl_url($_url));
					if($page->query->pageids){
						foreach($page->query->pageids as $result){
							$pageid = $result;
							break;
						}
						$object_name = '*';
						$wiki_content = $page->query->pages->$pageid->revisions[0]->$object_name; //We call it unestable, but it is beyond that :-)
						
						//Cleaning wiki content
						$results = array();
						$searches = array(
							'/<ref(.*)?>(.*)?<\/ref>/'
							,'/<small(.*)?>(.*)?<\/small>/'
							,'/<\/?ref\s?(.*)?\s?\/?>/'
							,'/\((.*)\)/'
							,'/{(.*)}/'
							,'/\[(.*)\]/'
							,'/<!--(.*)-->/'
						);
						$wiki_content = preg_replace($searches, ' ' ,$wiki_content);
						preg_match_all('/\|(.*)=(.*)/', $wiki_content, $results, PREG_SET_ORDER);
					
						//Filtering cleaner results
						$results_filtered = array();
						foreach($results as $result){
							if(substr_count($result[0],'|') > 1){
								$rs = explode('|',$result[0]);
								foreach($rs as $rs_s){
									array_push($results_filtered, $rs_s);
								}
							} else {
								array_push($results_filtered, $result[0]);							
							}
						}
					
						//Final string cleaning, it is when we get the data from wikipedia
						$searches = array("/=/","/\|/","/\n/","/'''?/","/\s\s+/");
						$replacements = array(' = ',' ',' ',' ',' ');

						//English wikipedia
						$array_names['eng'] = array(
							 'area_total_sq_mi =' => 'area'
							,'area_total_km2 = ' => 'area'
							,'population_est =' => 'population'
							,'population_total =' => 'population'
							,'established_date =' => 'settled'
							,'TotalAreaUS =' => 'area'
							,'2000Pop =' => 'population'
							,'area_km2 =' => 'area'
							,'population_estimate =' => 'population'
							,'area km2 =' => 'area'
							,'population =' => 'population'
							,'2010Pop =' => 'population'
						);
					
						//Spanish wikipedia
						$array_names['esp'] = array(
							 'superficie =' => 'area'
							,'población =' => 'population'
						);
						
						$data = array();
						foreach($results_filtered as $param){
							if(strlen($param) < 200 &&  strpos($param, "=") && $param != ""){
								$param = trim(preg_replace($searches, $replacements, $param));
								$i = 0;
								foreach($array_names[$lng] as $nm => $val){
									$pos = strpos($param, $nm);
									if($pos !== false){
										$param = trim(str_replace($nm, "", substr($param, $pos, strlen($param))));
										if(strlen($param) > 2){
											switch($val){
												case 'area':
													if(!isset($area_done)){
														$param = '<b>' . __("Area","onehundredcities") . ':</b> ' . $param;
														if(($i == 0 || $i == 5) && $lng == 'eng'){
															$param .= ' sq mi';
														} else {
															$param .= ' km2';
														}
														array_push($data, $param);
														$area_done = true;
													}
													break;
												case 'population':
													if(!isset($population_done)){
														$param = '<b>' . __("Population","onehundredcities") . ':</b> ' . $param;
														array_push($data, $param);
														$population_done = true;
													}
													break;
												case 'settled':
													if(!isset($settled_done)){
														$param = '<b>' . __("Settled","onehundredcities") . ':</b> ' . $param;
														array_push($data, $param);
														$settled_done = true;
													}
													break;
											}
										}
									}
									$i++;
								}
							}
						}
					
						$new_page = $this->curl_url("http://" . $lng_array[$lng] . ".wikipedia.org/w/api.php?action=opensearch&search=" . urlencode($location) ."&format=xml&limit=1");
						$xml = simplexml_load_string($new_page);
						$description = (string) preg_replace('/\([^)]*\)/s', '', $xml->Section->Item->Description);
						$description = preg_replace('/\s\s+/s', ' ', $description);
						$new_data = array(
							'title' => (string) $xml->Section->Item->Text, 
							'description' => $description, 
							'url' => (string) $xml->Section->Item->Url,
							'extra' => $data
						);
						if((string) $xml->Section->Item->Description) {
							$this->write_cache($new_data, $file_name);
							return $new_data;
						} else {
							return "";
						}
					}
				} else {
					return $data;
				}
			}

		} // End of Class
	
	}
	
	function wp_cityinfo($location, $lng = 'eng'){
		if(class_exists("OneHundredCities") && !$OneHundredCities){
			$OneHundredCities = new OneHundredCities();	
		}
		$data['location'] = $location;
		$data['lang'] = $lng;
		echo $OneHundredCities->insert_cities_template( $data );
	}
	
	add_action( 'init', 'init_one_hundred_cities');
	function init_one_hundred_cities(){
		if(class_exists("OneHundredCities") && !isset($OneHundredCities)){
			$OneHundredCities = new OneHundredCities();	
		}
	}
	
	//Include Widget Class
	include('100Cities-widget.php');
	
	add_action('widgets_init', 'register_cities_widget');
	function register_cities_widget() {
		register_widget('OneHundredCitiesWidget');
	}

	register_activation_hook( __FILE__, array('OneHundredCities', 'install'));
   
//End of file