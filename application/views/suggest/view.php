<?php

echo "<h2>Suggestions for <a href='/adventure/view/{$adventure['id']}/'>{$adventure['title']}</a></h2>";

$page_id = false;
$new_page = true;
$first = true;
foreach ($suggestions as $suggestion) {
	if($first) { $page_id = $suggestion['page']; $first = false; }
	else if($page_id != $suggestion['page']) {
		$page_id = $suggestion['page'];
		$new_page = true;
	}

	if($new_page) {
		if(!$first) echo "</ul>";
		echo "<h3>Suggestions for page: ";
		$firstname = true;
		foreach($suggestion['page_names'] as $names) {
			if($firstname) $firstname = false;
			else echo " / ";
			echo "<a href='/page/view/{$names['id']}/' target='_blank'>{$names['description']}</a>";
		}
		echo "</h3><a href=\"/suggest/denyall/$page_id/\" class=\"denyall-suggestions\">Deny all suggestions for this page.</a><ul>";
		$new_page = false;
	}
	echo "<li><em>{$suggestion['description']}</em> &mdash; Submitted {$suggestion['created']} <a href=\"/suggest/approve/{$suggestion['id']}/\">Approve</a> <a href=\"/suggest/deny/{$suggestion['id']}/\" class=\"deny-suggestion\">Deny</a></li>";
}
echo "</ul>";

?>