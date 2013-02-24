<h2>Editors for Adventure <a href="/adventure/view/<?php echo $adventure['id']; ?>"><?php echo $adventure['title']; ?></a></h2>
<?php if($editors) { ?><ul><?php
	foreach ($editors as $editor) {
		echo '<li>'.$editor['username'].' <a href="/adventure/remove_editor/'.$adventure['id'].'/'.$editor['user'].'">Remove</a></li>';
	} ?></ul><?php
} else echo "No editors added to this adventure yet."; ?>
<hr>
<?php 
echo validation_errors();
echo form_open('/adventure/editors/'.$adventure['id'])
	.form_label("Editor's Username", 'editor')
	.form_input('editor')
	.form_submit('submit', "Add Editor")
	.form_close(); 
?>