<h2>Delete Adventure: <?php echo $title; ?></h2>
<p><strong>BIG WARNING:</strong> If you delete an adventure, it is gone. Totally gone. Completely gone. It and <em>all</em> of it's content. <strong>All</strong> of it. It can't be brought back. It is unrecoverable. Know all of these things before you click the delete button below, OK?</p>
<?php echo form_open("adventure/delete/$id/"); ?>
	<input class="delete-adventure" id="deleteAdventure" type="submit" name="submit" value="Delete Adventure" /> <a href="/adventure/view/<?php echo $id; ?>">Nope. Don't delete.</a>
</form>