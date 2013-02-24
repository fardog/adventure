<h2>List of Creators</h2>
<ul>
	<?php 
	foreach ($creators as $creator) {
		echo "<li><a href=\"/creator/view/{$creator['user']}\">{$creator['name']}</a></li>";
	}
?>
</ul>