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

<div class="one-hundred-cities-box cx <?php if(isset($params['div']) && $params['div'] == 'float'){ ?> cities-float<?php } else { ?> cities-block<?php } ?>">
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
			echo '<br /><br />';
			foreach($params['wiki']['extra'] as $extra):
			echo $extra . '<br />'; 
			endforeach;
			echo '<br />';
			endif; 
			?>
		</p>
		<a class="one-hundred-wiki-link" href="<?php echo $params['wiki']['url']; ?>" title="<?php echo $params['wiki']['title']; ?>">Wikipedia</a>	
	</div>
	<?php endif; ?>
	
	<?php if(isset($params['panoramio_photos'])): ?>
	
	<div class="one-hundred-cities-photos cx">
		<?php 
			$i = 0;
			$count = count($params['panoramio_photos'])-1;
			foreach($params['panoramio_photos'] as $photo): 
		?>
		<div class="panoramio-photo<?php if($i == $count){ echo ' no-margin-right'; } ?>">
			<a target="_blank" href="<?php echo $photo['photo_url']; ?>"><img width="68" src="<?php echo $photo['photo_file_url']; ?>" alt="Panoramio photo by <?php echo $photo['owner_name']; ?>" /></a>
			<a target="_blank" class="panoramio-author" href="<?php echo $photo['owner_url']; ?>"><?php echo __("by","onehundredcities") . " " . $photo['owner_name']; ?></a>
		</div>
		<?php 
			$i++;
			endforeach;
		?>
	</div>
	
	<?php endif; ?>
	
	<?php if(isset($params['location_post'])): ?>
	
	<div class="one-hundred-cities-link">
		<p class="one-hundred-link-main-title"><?php echo $params['location_title']; ?></p>
		<span class="one-hundred-link-more"><?php  if($params['location_url'] != "") { ?><a target="_blank" href="<?php echo $params['location_url']; ?>" title="<?php echo $params['location_name']; ?>"><?php echo $params['location_name']; ?></a><?php } ?></span>
		<span class="one-hundred-link-date"><?php echo $params['location_post']['pubDate']; ?></span>
		<a class="one-hundred-link-title" target="_blank"  href="<?php echo $params['location_post']['link']; ?>" title="<?php echo $params['location_post']['title']; ?>"><?php echo $params['location_post']['title']; ?></a>
		<p class="one-hundred-link-desc"><?php echo substr($params['location_post']['description'], 0, 100) . "[...]"; ?></p>
	</div>
	
	<?php endif; ?>
	
	<?php if(isset($params['logo'])): ?>
		<a class="one-hundred-logo" target="_blank" href="<?php echo $params['logo']['link']; ?>" title="<?php echo $params['logo']['title']; ?>"><?php _e("created by ","onehundredcities") ?><img src="<?php echo $params['logo']['img']; ?>" alt="<?php echo $params['logo']['title']; ?>" /></a>
	<?php endif; ?>
</div>