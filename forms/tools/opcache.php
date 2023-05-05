<?php

use Dev4Press\Plugin\DebugPress\Main\OPCache;

if ( OPCache::instance()->status == 'restricted' ) {
	?>

	<p>
		<?php _e( "OPCache information can't be displayed, because access to OPCache statistics and information has been restricted on the hosting level.", "debugpress" ); ?>
	</p>

	<?php
} else {
	?>

	<div class="debugpress_info">
		<table>
			<thead>
			<tr>
				<th><?php _e( "Variable", "debugpress" ); ?></th>
				<th><?php _e( "Value", "debugpress" ); ?></th>
			</tr>
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
			<tr>
				<th><?php _e( "Variable", "debugpress" ); ?></th>
				<th><?php _e( "Value", "debugpress" ); ?></th>
			</tr>
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

		<h2><?php _e( "OPCache Memory Usage", "debugpress" ); ?></h2>
		<table>
			<thead>
			<tr>
				<th><?php _e( "Variable", "debugpress" ); ?></th>
				<th><?php _e( "Value", "debugpress" ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php

			$opcache_info = OPCache::instance()->memory;

			foreach ( $opcache_info as $var => $value ) {
				echo sprintf( '<tr><th>%s:</th><td>%s</td></tr>', $var, debugpress_rs( htmlspecialchars( $value ), false ) );
			}

			?>
			</tbody>
		</table>
	</div>

	<?php
}
