<?php
	/*
		Copyright 2013  Jonay Pelluz  (email : jonaypelluz@gmail.com)

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
	
	if(!class_exists("OneHundredCities_Options")){
		class OneHundredCities_Options {
			var $page = '';
			var $message = 0;
			var $data;
			
			function __construct() {
				add_action('admin_menu', array($this, 'init'));
				add_action('admin_head', array($this, 'admin_register_head'));
			}
			
			function justNumbers($input) {
				$input = preg_replace("/[^0-9]/","", $input);
				if($input == '') $input = 0;
				return $input;
			}

			function admin_register_head() {
				wp_register_style( 'admin-100Cities.css', '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/assets/admin-100Cities.css', null, '1.0', 'screen' );
				wp_enqueue_style( 'admin-100Cities.css' );
			}
			
			function init() {
				if (!current_user_can('update_plugins')){
					return;
				}
				$this->data = json_decode(get_option('one-hundred-cities-data'));
				$this->page = $page = add_options_page('100Cities', '100Cities', 'administrator', 'onehundredcities', array($this,'cities_page'));
			}
			
			function cities_page() {
				if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
					$data['gmaps'] = $this->justNumbers($_POST['gmaps']);
					$data['wikipedia'] = $this->justNumbers($_POST['wikipedia']);
					$data['panoramio'] = $this->justNumbers($_POST['panoramio']);
					$data['articles'] = $this->justNumbers($_POST['articles']);
					$data['tags'] = $this->justNumbers($_POST['tags']);
					$data['categories'] = $this->justNumbers($_POST['categories']);
					$data['logo'] = $this->justNumbers($_POST['logo']);
					$data = json_encode($data);
					update_option('one-hundred-cities-data', $data);
					$this->data = json_decode(get_option('one-hundred-cities-data'));
					$updated = true;
				}
				?>
				<form class="cities-form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<h1 class="cities-header"><span id="icon-tools" class="icon32"></span><?php _e("100 Cities Admin Panel","onehundredcities"); ?></h1>
					<?php if(isset($updated)){ ?><div class="cities-alert"><?php _e("100 Cities data updated","onehundredcities"); ?></div><?php } ?>
					<fieldset>
						<label class="cities-subheader"><?php echo _("Elements to show"); ?>:</label>
						<label><input type="checkbox" name="gmaps" value="1"<?php if($this->data->gmaps == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Google map","onehundredcities"); ?></label> 
						<label><input type="checkbox" name="wikipedia" value="1"<?php if($this->data->wikipedia == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Wikipedia description","onehundredcities"); ?></label>
						<label><input type="checkbox" name="panoramio" value="1"<?php if($this->data->panoramio == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Panoramio photos","onehundredcities"); ?></label>							
						<label><input type="checkbox" name="articles" value="1"<?php if($this->data->articles == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Related posts","onehundredcities"); ?></label>
					</fieldset>
					<fieldset>
						<label class="cities-subheader">
							<?php _e("Show plugin","onehundredcities"); ?>:<br />
							<small><?php _e("Dinamic sidebars has to be actived for this to work","onehundredcities"); ?></small><br />
							<small><?php echo __("It needs","onehundredcities"); ?> <a target="_blank" href="http://codex.wordpress.org/Function_Reference/wp_meta">wp_meta</a> <?php _e("on the sidebar","onehundredcities"); ?></small>
						</label>
						<label><input type="checkbox" name="tags" value="1"<?php if($this->data->tags == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Tag pages","onehundredcities"); ?></label>
						<label><input type="checkbox" name="categories" value="1"<?php if($this->data->categories == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Category pages","onehundredcities"); ?></label>
					</fieldset>
					<fieldset>
						<label class="cities-subheader"> <?php _e("Add our logo","onehundredcities"); ?></label>
						<label><input type="checkbox" name="logo" value="1"<?php if($this->data->logo == 1){ ?> checked="checked"<?php } ?> /> <?php _e("Keep our link, Thanks!","onehundredcities"); ?></label>
					</fieldset>
					<input class="button-primary" type="submit" name="Save" value="<?php _e("Save Options","onehundredcities"); ?>" id="submitbutton" />
				</form>
				<?php
			}

		}
	}

//End of file