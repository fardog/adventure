<?php if(!$edit) { ?>
<div id="history">
	<h3><a id="loadGame" href="/history/load/<?php echo $adventure_id; ?>">Load</a></h3>
	<h3><a id="saveGame" class="<?php echo $history_id; ?>" href="#">Save</a></h3>
	<h3><a id="getHistory" class="<?php echo $adventure_id; ?>" href="#">History</a></h3>
	<?php if($suggest) { ?>
	<h3><a id="suggestAction" href="#">Suggest an Action</a></h3>
	<div id="suggestDiv" style="display:none">
	<?php echo form_open('suggest/add/'.$link_id.'/'); ?>
		<div class="information">Help the author choose his next direction! Make a suggestion for an action on this page.</div>
		<input type="input" id="suggestText" name="suggestText" size="38" value="<?php echo set_value('suggestText'); ?>" />
		<input type="submit" name="submit" id="suggestSubmit" value="Suggest Action" />
	<?php echo form_close(); ?>
	</div>
	<?php } ?>
</div>
<?php } ?>

<h2><?php echo $header; ?></h2>

<?php
if(count($items) > 0) {	
	$i = 0;
	$len = count($items);
	foreach ($items as $item) {
		echo "<div class=\"contentBlock {$item['type']} content-{$item['id']}\">{$item['content']}</div>";
		if($edit == true) {
			echo "<div class=\"edit\"><ul>";
			if($i != 0) echo "<li><a href=\"/content/move/{$item['id']}/up/$link_id/\" title=\"Move Up\" class=\"contentMoveUp\">Move Up</a></li>";
			if($i != ($len-1))echo "<li><a href=\"/content/move/{$item['id']}/down/$link_id/\" title=\"Move Down\" class=\"contentMoveDown\">Move Down</a></li>";
			if($item['type'] == 'text') echo "<li><a class=\"edit-content\" href=\"/content/edit/{$item['id']}/$link_id/\" title=\"Edit Text\">Edit</a></li>";
			echo "<li><a class=\"delete-content\" href=\"/content/delete/{$item['id']}/$link_id/\" title=\"Delete Content\">Delete</a></li></ul></div>";
		}
		$i++;
	}
}
else {
	echo "<strong>There is nothing here yet.";
	if($editor) echo " <a href=\"#\" id=\"$link_id\" class=\"editLink page\">Add some sweet, sweet content?</a>";
	echo "</strong>";
}
	
?>