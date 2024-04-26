<div class="debugpress_phpinfo">
	<?php

	ob_start();
	phpinfo(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	$php_info = ob_get_contents();
	ob_end_clean();

	$php_info = str_replace( '</div></body></html>', '', $php_info );

	$ix       = strpos( $php_info, '<table' );
	$php_info = substr( $php_info, $ix );
	$php_info = debugpress_str_replace_first( '</table>', '</table><h2 class="phpsection">Basic Information</h2>', $php_info );
	$php_info = str_replace( '<table>', '<div class="table phpinfo"><table>', $php_info );
	$php_info = str_replace( '</table>', '</table></div>', $php_info );
	$php_info = str_replace( '<tr class="h"><th>', '<tr class="first with-header"><th class="first">', $php_info );
	$php_info = str_replace( '<td class="e">', '<td class="first b">', $php_info );
	$php_info = str_replace( '<td class="v">', '<td class="t">', $php_info );
	$php_info = str_replace( '<h1>PHP Credits</h1>', '', $php_info );
	$php_info = str_replace( '<h1>Configuration</h1>', '', $php_info );

	$ix       = strpos( $php_info, '<h2>PHP License' );
	$php_info = substr( $php_info, 0, $ix );
	$php_info = str_replace( '<h2>', '<h2 class="phpsection">', $php_info );

	echo $php_info; // phpcs:ignore WordPress.Security.EscapeOutput

	?>
</div>