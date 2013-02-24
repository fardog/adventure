	</div>
	<footer>
		<a href="http://fardogllc.com/" target="_blank">Far Dog LLC</a><div>Content is user provided. Use is subject to our <a href="/terms/">terms</a>.<br/><strong>Adventure</strong> is &copy 2012 Far Dog LLC. <a href="/changelog/">v<?php echo ADV_VERSION.ADV_ENVIRONMENT; ?></a>.</div>
	</footer>
	<?php if(!empty($flashmessage)) { echo "<div id=\"flashdata\">$flashmessage<a id=\"flashclose\" href=\"#\" title=\"Close\">x</a></div>"; } ?>
</div> <!--! end of #container -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>js/libs/jquery-1.7.1.min.js"><\/script>')</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script>window.jQuery.ui || document.write('<script src="<?php echo base_url(); ?>js/libs/jquery-ui-1.8.18.min.js"><\/script>')</script>
<!-- scripts concatenated and minified via ant build script-->
<script src="<?php echo base_url(); ?>js/plugins.js?ver=<?php echo ADV_VERSION; ?>"></script>
<script src="<?php echo base_url(); ?>js/script.js?ver=<?php echo ADV_VERSION; ?>"></script>
<!-- end scripts-->
<?php if(ADV_ENVIRONMENT != 'dev') { ?>
<script>
	var _gaq=[['_setAccount','UA-29503015-1'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<?php } ?>
<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
	<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
<![endif]-->
</body>
</html>
