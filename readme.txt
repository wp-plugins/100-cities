=== 100 Cities ===
Contributors: jonaypelluz, knoleskine 
Tags: city, travel, wikipedia, panoramio, feed, rss
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 2.5
Tested up to: 3.5.1
Stable tag: 1.0.1

100 Cities is a plugin that shows information about cities around the world, to be added when writing posts about those cities.

== Description ==

100 Cities is a plugin that shows information about cities around the world, to be added when writing posts about those cities. For any given city, 
it displays information, maps, photos, and related articles captured from several sources, including Google Maps, Wikipedia, Panoramio, and Knok.

100 Cities is developed by Knok, a home exchange website.

If you have suggestions for a new feature, feel free to email me at jonaypelluz@gmail.com

== Installation ==

1. Upload "100Cities" folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place "<?php wp_cityinfo('location-name', 'language'); ?>" in your templates. Default language is english, at the moment it is the only language we support.
4. Also you can place "[onehundredcities location='location-name' lang='eng/esp/ita' wiki='on/off' gmaps='on/off' panoramio='on/off' articles='on/off' logo='on/off']" in any post or page. If the attribute is on, you don't need to add it, it will use the value by default.
5. Also you can use the widget and place it on your widgets areas. You may need to modified the default css to ajust the plugin to those areas.
6. In the Dashboard you have an admin panel to control values by default.
7. If you want to use in tags and category listing, you need to add wp_meta() to the sidebar, where you want the plugin to be printed.
8. '/wp-content/plugins/100Cities/cache' folder needs write permissions, so the puglin will save data there and this reduce the quantity of resources that uses it.

== Screenshots ==

1. The plugin on the sidebar will look like this

== Changelog ==

= 1.0 =
* first version of the plugin, beta version.