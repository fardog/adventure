<nav>
	<ul>
		<li><?php
		if (!$this->tank_auth->is_logged_in()) echo "<a href=\"/auth/login/\">Login/Register</a>";
		else echo "<a href=\"/creator/view/{$this->tank_auth->get_user_id()}\">My Account</a>";			
		?>
		<li><a href="/help/">Help</a></li>
		<li><a href="/adventure/create/">Start New</a></li>
		<li><a href="/adventure/">Adventures</a></li>
		<?php 
		if($link_id) {
			if($editor) {
				if(!$edit) echo "<li><a href=\"#\" id=\"$link_id\" class=\"editLink page\">Edit this Page</a></li>";
				else echo "<li><a href=\"/page/view/$link_id\">Done Editing</a></li>";
				if($suggestions) echo "<li><strong><a href=\"/suggest/view/$adventure_id\">Suggestions ($suggestions)</a></strong></li>";
			}
			if($adventure_slug) {
				echo "<li><strong><a href=\"/adventure/$adventure_slug\">$title</a></strong></li>";
			}
		}
		if($adv_editor) {
			if(!$edit) echo "<li><a href=\"#\" id=\"$id\" class=\"editLink adventure\">Edit this Adventure</a></li>";
			else echo "<li><a href=\"/adventure/view/$id\">Cancel Editing</a></li>";
		}
		?>
	</ul>
</nav>