<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php if(isset($title)) { echo $title." | "; } echo "Adventure"; ?></title>
	<meta name="description" content="A multimedia 'choose your own adventure'-style mashup engine. Create your own stories.">
	<meta name="author" content="Far Dog LLC">
	<link rel="author" href="/humans.txt">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css?ver=<?php echo ADV_VERSION; ?>">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/smoothness/jquery-ui-1.8.18.custom.css?ver=<?php echo ADV_VERSION; ?>">
	<!-- OpenGraph Tags -->
	<?php
		$url = uri_string();
		if(empty($url)) {
			$og_type = 'website';
			$og_url = base_url();
		}
		else if ($creator_uid) {
			$og_type = 'profile';
			$og_username = $creator;
			$og_url = base_url()."creator/$creator/";
		}
		else {
			$og_type = 'article';
			$og_url = base_url()."adventure/{$adventure['slug']}/";
			if(!empty($creator)) $og_author = base_url()."creator/$creator/";
		}
	 	if(!empty($adventure['title'])) $og_title = $adventure['title'];
		else $og_title = "Adventure";
	 	if(!empty($adventure['description'])) $og_description = word_limiter($adventure['description'], 10);
		else $og_description = "A multimedia 'choose your own adventure'-style mashup engine. Create your own stories.";
		if(empty($adventure['image'])) $og_image = base_url().'opengraph-icon.png?type=square';
	?>
	
	<?php 
	if(isset($og_type)) echo "<meta xmlns:og=\"http://opengraphprotocol.org/schema/\" property=\"og:type\" content=\"$og_type\" />\n";
	if(isset($og_url)) echo "<meta xmlns:og=\"http://opengraphprotocol.org/schema/\" property=\"og:url\" content=\"$og_url\" />\n";
	if(isset($og_title)) echo "<meta xmlns:og=\"http://opengraphprotocol.org/schema/\" property=\"og:title\" content=\"$og_title\" />\n";
	if(isset($og_description)) echo "<meta xmlns:og=\"http://opengraphprotocol.org/schema/\" property=\"og:description\" content=\"$og_description\" />\n"; 
	if(isset($og_author)) echo "<meta xmlns:og=\"http://opengraphprotocol.org/schema/\" property=\"article:author\" content=\"$og_author\" />\n";
	if(isset($og_image)) echo "<meta xmlns:og=\"http://opengraphprotocol.org/schema/\" property=\"og:image\" content=\"$og_image\" />\n";
	?>
	
	<script src="<?php echo base_url(); ?>js/libs/modernizr-2.0.6.min.js?ver=<?php echo ADV_VERSION; ?>"></script>
	<?php if(!empty($css_embed)) { ?>
		<style type="text/css">
			<?php echo $css_embed; ?>
		</style>
	<?php } ?>
	<?php if(!empty($css_page)) { ?>
		<style type="text/css">
			<?php echo $css_page; ?>
		</style>
	<?php } ?>
</head>
<body>
<div id="container">
	<header>
		<h1><a href="/">Adventure</a></h1>
	</header>
	<div id="main" role="main">