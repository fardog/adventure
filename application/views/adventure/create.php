<h2><?php echo ($editing ? "Edit" : "Create")." an Adventure"; ?></h2>

<?php if(!$editing) { ?>
<div class="instructions">
<h3>So you want to create an Adventure?</h3>
<p>Give your Adventure a title, and an optional description. If you'd like to add any custom
	<a href="http://www.w3schools.com/cssref/" target="_blank">CSS</a>, which will be applied to <em>all</em> pages
	in your adventure, you can do that too.
</p>
<?php if($lock_possible) { ?>
<p>Since you've created an account, your adventure is only editable by you. If you'd like to let any old person edit
	it, check the "Allow Anonymous Edits" box below.</p>
<p>If you want to let visitors make suggestions on your adventure, you can set that below. Note that this is the
	default suggestion status&mdash;regardless of what you set below, you can close or open suggestions individually
	on each page of your adventure.</p>

<?php } else { ?>
<p>Since you're not logged in, the Adventure you're about to create will be <em>editable by everyone</em>. If you
	don't want that, make sure you <a href="/auth/login/">register and log in first</a>.</p>

<?php } ?>
</div>
<?php } ?>

<?php echo form_open('adventure/'.($editing ? "edit/$id" : 'create')); ?>
<table>
	<tr>
		<td><label for="title">Title</label></td>
		<td><input type="input" name="title" value="<?php echo set_value('title'); echo $adv_title; ?>" /><div class="error"><?php echo form_error('title'); ?></div></td>
	</tr>
	<tr>
		<td><label for="description">Description (<a href="http://textile.thresholdstate.com/" target="_blank">Textile</a>)</label></td>
		<td><textarea name="description"><?php echo set_value('description'); echo $description; ?></textarea><div class="error"><?php echo form_error('description'); ?></div></td>
	</tr>
	<tr>
		<td><label for="css">Style (<a href="http://www.w3schools.com/cssref/" target="_blank">CSS</a>)</label></td>
		<td><textarea name="css"><?php echo set_value('css'); echo $css; ?></textarea><div class="error"><?php echo form_error('css'); ?></div></td>
	</tr>
	<?php if($lock_possible) { ?>
	<tr>
		<td><label for="locked">Allow anonymous edits</label></td>
		<td><input type="checkbox" name="locked" value="lock" <?php 
			if(isset($locked)) echo ($locked ? 'checked' : '');
			else echo set_checkbox('locked', 'lock', FALSE); 
			?> /></td>
	</tr>
	<tr>
		<?php switch($suggest) {
			case 1:
				$registered = "checked";
				break;
			case 2:
				$anonymous = "checked";
				break;
			default:
				$none = "checked";
				break; 
			} ?>
		<td><label for="suggest">Default Suggestion Status</label></td>
		<td><input type="radio" name="suggest" value="0" <?php echo $none; ?>><label>Suggestions not allowed</label><br/>
			<input type="radio" name="suggest" value="1" <?php echo $registered; ?>><label>Suggestions allowed by registered users</label><br/>
			<input type="radio" name="suggest" value="2" <?php echo $anonymous; ?>><label>Suggestions allowed by registered and anonymous users</label>
		</td>
	<?php } ?>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" name="submit" value="<?php echo ($editing ? 'Edit' : 'Start').' Adventure'; ?>" />
		</td>
	</tr>
</table>
</form>