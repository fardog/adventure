<div class="editor">
<h2>Add Content</h2>

<?php 
echo form_open('page/edit/'.$link_id.'/');
echo "<div class=\"error\">".validation_errors().$misc_error."</div>";
echo form_fieldset('Video / Audio (<a href="http://youtube.com/" target="_blank">Youtube</a>)');
?>
	<label for="video">URL or ID (<a href="/help/#youtube" title="Help for Video embedding">Help</a>)</label>
	<input type="input" name="video" value="<?php echo set_value('video'); ?>" />
	<a href="#" class="showHide">Show Options</a>
	<div id="options" style="display:none">
	<?php 
		echo form_checkbox('videoAutoplay', '1', false).form_label('Autoplay', 'videoAutoplay').'<br/>';
		echo form_checkbox('videoAudio', '1', false).form_label('Audio-Only', 'videoAudio').'<br/>';
		//echo form_checkbox('videoMute', '1', false).form_label('Mute Audio', 'videoMute').'<br/>';
		echo form_checkbox('videoLoop', '1', false).form_label('Loop', 'videoLoop').'<br/>';
		echo form_checkbox('videoHD', '1', false).form_label('High Definition', 'videoHD').'<br/>';
		echo form_input(array(name=>'videoStart',size=>3)).' '.form_label('Start Time (in seconds)').'<br/>';
	?>
	<div class="information"><strong>NOTE:</strong> Embed options are only used when in "view" mode. In edit mode, you will see the video without the above options enabled.</div>
	<div class="error"><?php echo form_error('video'); ?></div>

<?php 
echo form_fieldset_close(); 
echo form_fieldset('Image (<a href="http://imgur.com/" target="_blank">imgur</a>)');
?>
	<label for="image">URL or ID (<a href="/help/#imgur" title="Help for Image embedding">Help</a>)</label>
	<input type="input" name="image" value="<?php echo set_value('image'); ?>" />
	<a href="#" class="showHide">Show Options</a>
	<div id="options" style="display:none">
	<?php 
		echo form_input(array(name=>'imageWidth',size=>'3')).' &times; '.form_input(array(name=>'imageHeight',size=>'3'));
		echo ' '.form_label('Width &times; Height', 'imageWidth').'<br/>';
	?>
	<div class="information"><strong>NOTE:</strong> Image widths exceeding 960px will be ignored. You may enter a width or a height only for proportional resizing.</div>
	<div class="error"><?php echo form_error('image'); ?></div>
<?php 
echo form_fieldset_close();
echo form_fieldset('Audio (<a href="http://soundcloud.com/" target="_blank">SoundCloud</a>)');
?>
	
	<label for="audio">URL (<a href="/help/#soundcloud" title="Help for Audio embedding">Help</a>)</label>
	<input type="input" name="audio" value="<?php echo set_value('sound'); ?>" />
	<a href="#" class="showHide">Show Options</a>
	<div id="options" style="display:none">
	<?php 
		echo form_checkbox('audioAutoplay', '1', true).form_label('Autoplay', 'audioAutoplay').'<br/>';
	?>
	<div class="information"><strong>NOTE:</strong> Embed options are only used when in "view" mode. In edit mode, the audio will not autoplay.</div>
	<div class="error"><?php echo form_error('audio'); ?></div>
<?php
echo form_fieldset_close();
echo form_fieldset('Text (<a href="http://textile.thresholdstate.com/" target="_blank">Textile</a>)');
?>
	
	<textarea name="text"><?php echo set_value('text'); ?></textarea>
	<div class="error"><?php echo form_error('text'); ?></div>

<?php
echo form_fieldset_close();
?>

	<input type="submit" name="submit" id="addContent" value="Add Content" />

<?php echo form_close(); ?>
</div>

<div class="editor">
	<h2>Page Options</h2>
<?php 
echo form_open('page/options/'.$link_id.'/');
echo form_fieldset('Page Style (<a href="http://www.w3schools.com/cssref/" target="_blank">CSS</a>)');
$css_value = set_value('css');
if(empty($css_value)) $css_value = $css_edit;
?>
	
	<textarea name="css"><?php echo $css_value ?></textarea>

<?php 
echo form_fieldset_close();
if (!$suggestions_disabled) {
	echo form_fieldset('Suggestions (<a href="/help/#suggestions" target="_blank">Help</a>)');
	echo form_radio('allowSuggestions', 'none', (($suggest_none == false) ? 0 : 1)).form_label('None Allowed').'<br/>';
	echo form_radio('allowSuggestions', 'authenticated', (($suggest_auth == false) ? 0:1)).form_label('Authenticated Users').'<br/>';
	echo form_radio('allowSuggestions', 'all', (($suggest_all == false) ? 0:1)).form_label('All Visitors').'<br/>';
	?> 
		<label for="suggestTimeout">Allowed Until&hellip;</label>
		<input size="7" type="input" name="suggestTimeout" id="suggestTimeout" class="datepicker" value="<?php echo (empty($suggestTimeout) ? "Forever" : date("Y-m-d",strtotime($suggestTimeout))); ?>" />
		<a id="timeoutNone" href="#">Forever</a>
	<?php 
	echo form_fieldset_close();
}
?>
	<div class="error"><?php echo $options_error; ?></div>
	<input type="submit" name="submit" id="saveOptions" value="Save Options" />
<?php echo form_close(); ?>
</div>
