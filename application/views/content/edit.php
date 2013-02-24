<h2>Edit Content</h2>
<?php echo form_open("/content/edit/$id/$link_id/"); ?>

	<label for="text">Text (<a href="http://textile.thresholdstate.com/" target="_blank">Textile</a>)</label>
	<textarea name="text"><?php echo set_value('text'); echo $adv_text; ?></textarea><div class="error"><?php echo form_error('text'); ?></div><br/>

	<input type="submit" name="submit" value="Edit Content" />
</form>