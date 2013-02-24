<div class="advList">
<h2><?php if(!empty($list_title)) echo $list_title; else echo "List of Adventures"; ?></h2>

<ul class="<?php echo (empty($adventures[0]['name']) === TRUE ? "creatorList" : "siteList"); ?>">
<?php
foreach ($adventures as $adventure) {
	//echo "<li><img src=''>";
	echo "<li><strong><a href=\"/adventure/{$adventure['slug']}/\">{$adventure['title']}</a></strong>";
	if(!empty($adventure['name'])) echo " (by <a href=\"/creator/".strtolower($adventure['name'])."\">{$adventure['name']}</a>)";
	echo '<span class="advDescription">'.word_limiter($adventure['plain_desc'], 10).'&nbsp;</span></li>';
}
?>
</ul>
</div>