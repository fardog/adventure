<!DOCTYPE html>
<html lang="en">
<head>
<title>Database Explode</title>
<style type="text/css">

	html { height: 100%; margin-bottom: 1px; }
	#junk { position: absolute; bottom: 5px; right: 15px; text-align: right; font-size: 8pt;}
	body { font-family: Verdana, "Bitstream Vera Sans", sans-serif; font-size: 10pt; line-height: 1.9em; color: #333; background: #fff; text-rendering: optimizeLegibility; }
	#container { text-align: center; margin: 40px auto 40px auto; width: 960px;}
	ul { list-style-type: none; }
	h1 { line-height: 24px; font-size: 24px; }
	.adventure { text-decoration: none; display: inline-block; width: 192px; height: 24px; text-indent: -9999px; background: url(/css/images/adventure.gif) top left no-repeat;}
	#error { border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;}
	footer { 
	   margin-top: 20px; 
	   padding-top: 5px; 
	   text-align: right;
	   clear: both;
	   font-size: 10px;
	   line-height: 13px;
	}
	   
	footer a[href*=fardogllc] {
	   float: right;
	   display: block;
	   width: 32px; height: 24px;
	   background: url(/css/images/fardog-black.png) top left no-repeat;
	   line-height: 300px;
	   overflow: hidden;
	   margin: 0 5px 0 7px;
	}
</style>
</head>
<body>
	<div id="container">
		<h1><a href="/" class="adventure">Adventure</a> had a problem&hellip;</h1>
		<div id="error">
			<?php echo $message; ?>
			<p>We hope you don't hate us forever. <a href="/">Go home?</a></p>
		</div>
	<footer>
		<a href="http://fardogllc.com/" target="_blank">Far Dog LLC</a><div>Content is user provided. Use is subject to our <a href="/terms/">terms</a>.<br/><strong>Adventure</strong> is &copy 2012 Far Dog LLC. <a href="/changelog/">v1.115live</a>.</div>
	</footer>
	</div> <!--! end of #container -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="http://adventure.fardo.gs/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script>window.jQuery.ui || document.write('<script src="http://adventure.fardo.gs/js/libs/jquery-ui-1.8.18.min.js"><\/script>')</script>
<!-- scripts concatenated and minified via ant build script-->
<script src="http://adventure.fardo.gs/js/plugins.js?ver=1.115"></script>
<script src="http://adventure.fardo.gs/js/script.js?ver=1.115"></script>
<!-- end scripts-->
<script>
	var _gaq=[['_setAccount','UA-29503015-1'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
	<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
<![endif]-->
</body>
</html>