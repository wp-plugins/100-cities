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
	
	class OneHundredCitiesWidget extends WP_Widget {

		//Constructor
		function OneHundredCitiesWidget() {
			parent::WP_Widget(false, '100 Cities');
		}

		//Widget form creation
		function form( $instance ) {	
			// Check values
			if( $instance) {
				 $title = esc_attr($instance['title']);
				 $lng = esc_attr($instance['lng']);
			} else {
				 $title = '';
				 $lng = '';
			}
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Add city location</label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('lng'); ?>">Add language</label>
				<select class="widefat" id="<?php echo $this->get_field_id('lng'); ?>" name="<?php echo $this->get_field_name('lng'); ?>">
					<option value="eng">English</option>
				</select>
			</p>
		<?php
		}

		//Widget update
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			//Fields
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['lng'] = strip_tags($new_instance['lng']);
			return $instance;
		}

		//Widget display
		function widget( $args, $instance ) {
			extract( $args );
			$data['location'] = apply_filters('widget_title', $instance['title']);
			$data['lng'] = $instance['lng'];
			if(class_exists("OneHundredCities") && !$OneHundredCities){
				$OneHundredCities = new OneHundredCities();	
			}
			echo $before_widget;
			echo $OneHundredCities->insert_cities_template( $data );
			echo $after_widget;
		}
	}

//End of the widget