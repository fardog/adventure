<?php
if(count($adventures) < 1) {
	if($you) echo "<p><strong>You haven't created an adventure yet. <a href=\"/adventure/create/\">Create one?</a></strong></p>";
	else echo "<p><strong>This user hasn't created an adventure yet.</strong></p>";
}
?>