=== 100 Cities ===
Contributors: jonaypelluz, knoleskine 
Tags: city, travel, wikipedia, panoramio, feed, sidebar, posts, widget, plugin, images
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 2.5
Tested up to: 4.2.3
Stable tag: 1.5

Add great content to your posts automatically: just choose a city and get a map, photos, data, and related offers. Very useful for travel bloggers!

== Description ==

100 Cities is a plugin which displays information about cities around the world, recommended for writing about cities/destinations. For any given city, it finds general information, maps, photos, and accommodation offers, captured from several sources, including Google Maps, Wikipedia and Panoramio.

100 Cities is developed by Knok, a home exchange website.

If you have suggestions for a new feature, feel free to email me at jonaypelluz@gmail.com

== Installation ==

1. Upload "100-cities" folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place "wp_cityinfo('location-name', 'language');" inside php tags in any of your templates and the widget will show up if the tag name is the name of a city. Default language is English (at the moment it is the only language we support.)
4. Also you can place "[onehundredcities location='location-name' mapzoom='8' weather='on/off' lang='eng/esp/ita' panoramiocount='3' div='block/float' wiki='on/off' gmaps='on/off' panoramio='on/off' offers='on/off' logo='on/off']" in any post or page. If the attribute is on in the admin area, you don't need to add it, it will use the value by default.
5. The location name must be written as it appears in the Wikipedia URL.
6. Also you can use the widget and place it on your widgets area. You may need to modify the default css to adjust the plugin for that area.
7. In the Dashboard you have an admin panel to control values by default, css and more.
8. If you want to use tags and category listings, you need to add wp_meta() to the sidebar, where you want the plugin to be.
9. '/wp-content/plugins/100-cities/cache' folder needs write permissions, so the plugin will save data there and this reduces the quantity of resources that uses it.
10. '/wp-content/plugins/100-cities/assets/offers.data' file has data written in json and you can change it to write your own offers

== Frequently Asked Questions ==

= I have changed the feed url but the information and links on related posts remain the same =

The cache last for 2 days, after two days it will read the feed again for that city, if you want to delete the cache it is in the folder called "cache" in the plugin's directory ("/wp-content/plugins/100-cities/cache/")

= My posts take longer to show up =

This will occur the first time you publish a post (as the plugin is accumulating the data for the first time). Double check point 8 of the installation process to make sure the writing permissions for the cache folder are turned on and if it is on, don't worry be happy, our plugin is not slowing down your website.

= The city info doesn't show up =

This happens because the name of the city ('location') has to be equal, ('lowercase or uppercase') to the url in wikipedia. Ex.: for San Francisco ('https://en.wikipedia.org/wiki/San_Francisco') we have to write: San_Francisco.

== Screenshots ==

1. With all the sections of the plugin.
2. Show the parts you can hide or show.
3. How it looks inside a post.

== Changelog ==

= From 1.0 to 1.2 =
* First version of the plugin, beta version.
* Changes to the screenshots and readme file
* Small fix to a function in admin area
* Feed can be modified from the admin area and the plugin inside a post have a new attribute, it can float to one of the sides or occupy 100% of the width of the post.
* Css url fixed, add more regular expression to get info from wikipedia, redesign, add param to control panoramio photo count

= 1.3 =
* Fixed some problems with wikipedia information, improve regular expressions to get the data and filter it
* Add form to change widget CSS easily
* Add param to control map zoom out
* Change main functions so in a future we could add more languages
* Add more FAQ

= 1.4 =
* Add weather information to the cities
* Add more regular expressions to fix some errors

= 1.5 =
* Remove RSS articles by default, in future releases it would be possible to add it directly to the widget
* Add offers for accommodation or other services. They are located in assets and you can add your own, so they will show up in your widget


