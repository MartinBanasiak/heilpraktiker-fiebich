	<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/flashplayer/swfobject.js"></script>
 
	<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="<?= $flash_width ?>" height="<?= $flash_height?>">
		<param name="movie" value="/plugins/flashplayer/player.swf" />
		<param name="allowfullscreen" value="true" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="file=<?= $flash_file ?>&backcolor=e7eef6&frontcolor=005598&lightcolor=005598&screencolor=FFFFFF" />
		<object type="application/x-shockwave-flash" data="/plugins/flashplayer/player.swf" width="<?= $flash_width ?>" height="<?= $flash_height ?>">
			<param name="movie" value="/plugins/flashplayer/player.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="flashvars" value="file=<?= $flash_file ?>&backcolor=e7eef6&frontcolor=005598&lightcolor=005598&screencolor=FFFFFF" />
			<p><a href="http://get.adobe.com/flashplayer">Laden Sie Flash herunter</a><br />um dieses Video zu sehen</p>
		</object>
	</object>
