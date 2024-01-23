<?php

use Dev4Press\Plugin\DebugPress\Main\OPCache;

?>
<p>
	<?php esc_html_e( 'This page shows several Information panels that are not practical for display in the Debugger window due to their size and fact that they don\'t usually change between page loading.', 'debugpress' ); ?>
</p>

<h2><?php esc_html_e( 'PHP Info', 'debugpress' ); ?></h2>
<p>
	<?php esc_html_e( 'Load the full content of the PHP.ini file through PHPInfo function call.', 'debugpress' ); ?>
</p>
<a href="tools.php?page=debugpress-info&tab=php" class="button-primary"><?php esc_html_e( 'Open the Panel', 'debugpress' ); ?></a>

<?php if ( OPCache::instance()->has_opcache() ) { ?>
    <h2><?php esc_html_e( 'OPCache Info', 'debugpress' ); ?></h2>
    <p>
		<?php esc_html_e( 'Show the settings for the PHP OPCache and basic statistics.', 'debugpress' ); ?>
    </p>
    <a href="tools.php?page=debugpress-info&tab=opcache" class="button-primary"><?php esc_html_e( 'Open the Panel', 'debugpress' ); ?></a>
<?php } ?>

<h2><?php esc_html_e( 'MySQL Variables', 'debugpress' ); ?></h2>
<p>
	<?php esc_html_e( 'Show all the MySQL configuration variables retrieved from the database server.', 'debugpress' ); ?>
</p>
<a href="tools.php?page=debugpress-info&tab=mysql" class="button-primary"><?php esc_html_e( 'Open the Panel', 'debugpress' ); ?></a>
