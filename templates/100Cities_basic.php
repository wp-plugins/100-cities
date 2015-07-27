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
?>

<div class="one-hundred-cities-box cities-clx <?php if(isset($params['div']) && $params['div'] == 'float'){ ?> cities-float<?php } else { ?> cities-block<?php } ?>">
	<?php if(isset($params['map_width'])): ?>
	<div class="one-hundred-cities-map">	
		<img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo urlencode($params['location']); ?>&zoom=<?php echo $params['zoom']; ?>&size=<?php echo $params['map_width']; ?>x200&sensor=false" alt="<?php echo $params['location']; ?>"/>
	</div>
	<?php endif; ?>
	<?php if(isset($params['wiki'])): ?>
	<div class="one-hundred-cities-wiki">
		<p class="one-hundred-cities-title"><?php echo $params['wiki']['title']; ?></p>
		<p class="one-hundred-cities-description">
			<?php echo $params['wiki']['description']; ?>
			<?php 
			if(!empty($params['wiki']['extra'])):
				echo '<br />';
				foreach($params['wiki']['extra'] as $extra):
					echo $extra . '<br />'; 
				endforeach;
			else:
				echo '<br />'; 
			endif;
			if(!empty($params['weather'])):
				$weather_text = "<b>" . __("Weather","onehundredcities") . "</b>:";
				if(!empty($params['weather']['temperature'])){
					$weather_text .= " " . $params['weather']['temperature'];
				}
				if(!empty($params['weather']['wind-speed'])){
					$weather_text .= ", " . __("Wind at","onehundredcities") . " " . $params['weather']['wind-speed'];
				}
				if(!empty($params['weather']['humidity'])){
					$weather_text .= ", " . $params['weather']['humidity'];
				}
				if(!empty($params['weather']['weather-desc'])){
					$weather_text .= ", '" . $params['weather']['weather-desc'] . "'";
				}
				echo $weather_text. "<br />";
			endif;
			?>
		</p>
		<a class="one-hundred-wiki-link" href="<?php echo $params['wiki']['url']; ?>" title="<?php echo $params['wiki']['title']; ?>">Wikipedia</a>	
	</div>
	<?php endif; ?>
	
	<?php if(isset($params['panoramio_photos'])): ?>
	
	<ul class="one-hundred-cities-photos cities-clx">
		<?php 
			$i = 0;
			$count = count($params['panoramio_photos'])-1;
			foreach($params['panoramio_photos'] as $photo): 
		?>
		<li class="panoramio-photo<?php if($i == $count){ echo ' no-padding-right'; } ?>">
			<a target="_blank" href="<?php echo $photo['photo_url']; ?>"><img width="60" src="<?php echo $photo['photo_file_url']; ?>" alt="Panoramio photo by <?php echo $photo['owner_name']; ?>" /></a>
			<?php 
				if(strlen($photo['owner_name']) > 10){
					$owner_name = substr($photo['owner_name'], 0, 10) . '...';
				} else {
					$owner_name = $photo['owner_name'];
				}
			
			?>
			<a target="_blank" class="panoramio-author" href="<?php echo $photo['owner_url']; ?>"><?php echo __("by","onehundredcities") . " " . $owner_name; ?></a>
		</li>
		<?php 
			$i++;
			endforeach;
		?>
	</ul>
	
	<?php endif; ?>
	
	<?php if(isset($params['offers_title'])): ?>
	
	<div class="one-hundred-cities-link">
		<p class="one-hundred-link-main-title"><?php echo $params['offers_title']; ?></p>
		<span class="one-hundred-link-more"><a target="_blank" href="<?php echo $params['offers_url']; ?>" title="<?php echo $params['offers_more']; ?>"><?php echo $params['offers_more']; ?></a></span>
	</div>
	
	<?php endif; ?>
	
	<?php if(isset($params['logo'])): ?>
		<a class="one-hundred-logo" target="_blank" href="<?php echo $params['logo']['link']; ?>" title="<?php echo $params['logo']['title']; ?>"><?php _e("created by ","onehundredcities") ?> Knok</a>
	<?php endif; ?>
</div>