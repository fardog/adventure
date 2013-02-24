<div id="history">
	<h3><a id="loadGame" href="/history/load/<?php echo $id; ?>">Load</a></h3>
	<h3><a id="getHistory" class="<?php echo $id; ?>" href="#">History</a></h3><ul></ul>
</div>
<h2><?php echo $title; ?></h2>
<p><?php echo $description; ?></p>
<h3><a href="/adventure/<?php echo $slug.'/'.$start; ?>">Start this Adventure!</a></h3>
<ul>
	<li>Created by <strong><?php echo "<a href=\"/creator/".strtolower($creator)."\">$creator</a>"; ?></strong>.</li>
	<?php if($editors) { ?>
	<li>Editors: <?php
		for($i = 0; $i < count($editors); $i++) {
			echo "<strong><a href=\"/creator/".strtolower($editors[$i]['username'])."\">{$editors[$i]['username']}</a></strong>";
			if($i+1 != count($editors)) echo ", ";
		} ?></li>
	<?php } ?>
	<li>There are <strong><a href="/adventure/<?php echo $slug; ?>/pages">(<?php echo $pages_count; ?>)</a></strong> pages in this adventure.</li>
	<li>Anonymous users <strong><?php echo ($locked == 'yes' ? 'cannot' : 'can'); ?></strong> make edits.</li>
	<?php if($suggestion_count) { //show suggestions to approve, if you're the author ?>
	<li><a href="/suggest/view/<?php echo $id; ?>">You have <strong>(<?php echo $suggestion_count; ?>)</strong> suggestion<?php echo ($suggestion_count > 1 ? 's' : ''); ?> to approve!</a></li>
	<?php } if($suggest_page_count > 0) { ?>
	<li>Suggestions are open on <strong>(<?php echo $suggest_page_count; ?>)</strong> pages.
	<?php if($adv_delete) { ?>
		<a href="/suggest/closeall/<?php echo $id; ?>" class="closeall-suggestions">Close all?</a>
	<?php } } ?></li>
	<?php if($adv_delete) { //show deletion link if available ?>
	<li><a href="/adventure/editors/<?php echo $id; ?>">Add or remove Editors</a></li>
	<li><a href="/adventure/delete/<?php echo $id; ?>">Delete this Adventure</a></li>
	<?php } ?>
</ul>