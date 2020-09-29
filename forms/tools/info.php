<?php

use Dev4Press\Plugin\DebugPress\Main\OPCache;

?>
<p>
	<?php _e( "This page shows several Information panels that are not practical for display in the Debugger window due to their size and fact that they don't usually change between page loading.", "debugpress" ); ?>
</p>

<h2><?php _e( "PHP Info", "debugpress" ); ?></h2>
<p>
	<?php _e( "Load the full content of the PHP.ini file through PHPInfo function call.", "debugpress" ); ?>
</p>
<a href="tools.php?page=debugpress&tab=php" class="button-primary"><?php _e( "Open the Panel", "debugpress" ); ?></a>

<?php if ( OPCache::instance()->has_opcache() ) { ?>
    <h2><?php _e( "OPCache Info", "debugpress" ); ?></h2>
    <p>
		<?php _e( "Show the settings for the PHP OPCache and basic statistics.", "debugpress" ); ?>
    </p>
    <a href="tools.php?page=debugpress&tab=opcache" class="button-primary"><?php _e( "Open the Panel", "debugpress" ); ?></a>
<?php } ?>

<h2><?php _e( "MySQL Variables", "debugpress" ); ?></h2>
<p>
	<?php _e( "Show all the MySQL configuration variables retrieved from the database server.", "debugpress" ); ?>
</p>
<a href="tools.php?page=debugpress&tab=mysql" class="button-primary"><?php _e( "Open the Panel", "debugpress" ); ?></a>