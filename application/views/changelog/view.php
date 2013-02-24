<div id="changelog">
<?php 
foreach ($items as $item) {
    echo "<div class=\"changeitem\"><em>Published: ".$item['pubDate']."</em>".$item['title']."</div>";
} ?>
</div>