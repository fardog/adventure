<h2><?php echo ($editing ? "Edit" : "Create")." a Static Page"; ?></h2>

<?php echo form_open('static_page/'.($editing ? "edit/$id" : 'create')); ?>
<table>
	<tr>
		<td><label for="name">Name</label></td>
		<td><input type="input" name="name" value="<?php echo set_value('name'); echo $name; ?>" /><div class="error"><?php echo form_error('title'); ?></div></td>
	</tr>
	<tr>
		<td><label for="description">Description (<a href="http://txt.io/static/a/textile.html" target="_blank">Textile</a>)</label></td>
		<td><textarea name="description"><?php echo set_value('description'); echo $description; ?></textarea><div class="error"><?php echo form_error('description'); ?></div></td>
	</tr>
	<tr>
		<td><label for="content">Content (<a href="http://txt.io/static/a/textile.html" target="_blank">Textile</a>)</label></td>
		<td><textarea name="content"><?php echo set_value('content'); echo $content; ?></textarea><div class="error"><?php echo form_error('content'); ?></div></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="<?php echo ($editing ? 'Edit' : 'Create').' Static Page'; ?>" /></td>
	</tr>
</table>
</form>