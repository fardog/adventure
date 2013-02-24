<div id="answers">
	<ul>
<?php

if(count($links) > 0) {
	$i = 0;
	$len = count($links);
	foreach ($links as $link) {
		echo '<li><a href="/adventure/'.$slug.'/'.$link['id'].'/">'.$link['description'].'</a>';
		if($edit == true) {
			echo "<ul>";
			if($i != 0) echo "<li><a href=\"/links/move/{$link['id']}/up/$link_id/\" title=\"Move Up\" class=\"linkMoveUp\">Move Up</a></li>";
			if($i != ($len-1)) echo "<li><a href=\"/links/move/{$link['id']}/down/$link_id/\" title=\"Move Down\" class=\"linkMoveDown\">Move Down</a></li>";
			echo "<li><a class=\"edit-links\" href=\"/links/edit/{$link['id']}/$link_id/\" title=\"Edit Link Text\">Edit</a></li>";
			echo "<li><a class=\"delete-links\" href=\"/links/delete/{$link['id']}/$link_id/\" title=\"Delete Link\">Delete</a></li></ul>";
		}
		echo '</li>';
		$i++;
	}
}
else {
	if($edit) echo "<li>There are no actions added yet.</li>";
}

?>
	</ul>
</div>