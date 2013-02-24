<h3>List of Pages</h3>
<ul>
<?php
foreach($pages as $page) {
	echo "<li>Page ID: {$page['destination']} &gt; <a href=\"/adventure/$slug/{$page['id']}\">{$page['description']}</a></li>";
}
?>
</ul>