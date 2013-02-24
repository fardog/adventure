<h2>Edit Link Text</h2>
<?php echo form_open("links/edit/$id/$link_id/"); ?>

	<label for="description">Describe Action</label>
	<input type="input" name="description" value="<?php echo set_value('description'); echo $adv_description; ?>" />
	
	<div class="error"><?php echo form_error('description'); ?></div>
	
	<input type="submit" name="submit" value="Edit Link" /> 
</form>
