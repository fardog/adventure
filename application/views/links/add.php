<div class="editor">
<h2>Add Actions</h2>

<h3>New Page</h3>
<?php echo form_open('links/add/'.$link_id.'/'); ?>

	<label for="description">Describe Action</label>
	<input type="input" name="description" value="<?php echo set_value('description'); ?>" />
	
	<div class="error"><?php echo $add_error; ?></div>
	
	<input type="submit" name="submit" value="Add New Page" id="addLink" /> 
</form>


<h3>Existing Page</h3>
<?php echo form_open('links/existing/'.$link_id.'/'); ?>

	<label for="desc_exists">Describe Action</label>
	<input type="input" name="desc_exists" value="<?php echo set_value('desc_exists'); ?>" />

<?php 
$page_list = array();
foreach ($pages as $p) {
	$destination = $p['destination'];
	if($destination == $page) continue;
	if(!empty($page_list[$destination])) $page_list[$destination] .= ' / '.$p['description'];
	else $page_list[$destination] = $destination.': '.$p['description'];
}
?>
	
	<label for="destination">Existing Page ID</label>
	<?php echo form_dropdown('destination', $page_list); ?>
	
	<div class="error"><?php echo $exist_error; ?></div>
	
	<input type="submit" name="submit" value="Add Existing Page" id="addExistingLink" /> 
</form>

</div>