<?php

use Dev4Press\Plugin\DebugPress\Main\OPCache;

?>
<div class="debugpress_info">
    <table>
        <thead>
        <th><?php _e( "Variable", "debugpress" ); ?></th>
        <th><?php _e( "Value", "debugpress" ); ?></th>
        </thead>
        <tbody>
		<?php

		$opcache_info = OPCache::instance()->settings;

		foreach ( $opcache_info as $var => $value ) {
			echo sprintf( '<tr><th>%s:</th><td>%s</td></tr>', $var, debugpress_rs( htmlspecialchars( $value ), false ) );
		}

		?>
        </tbody>
    </table>

    <h2><?php _e( "OPCache Statistics", "debugpress" ); ?></h2>
    <table>
        <thead>
        <th><?php _e( "Variable", "debugpress" ); ?></th>
        <th><?php _e( "Value", "debugpress" ); ?></th>
        </thead>
        <tbody>
		<?php

		$opcache_info = OPCache::instance()->statistics;

		foreach ( $opcache_info as $var => $value ) {
			echo sprintf( '<tr><th>%s:</th><td>%s</td></tr>', $var, debugpress_rs( htmlspecialchars( $value ), false ) );
		}

		?>
        </tbody>
    </table>
</div>