<div class="debugpress_info">
    <table>
        <thead>
        <th><?php _e( "Variable", "debugpress" ); ?></th>
        <th><?php _e( "Value", "debugpress" ); ?></th>
        </thead>
        <tbody>
		<?php

		$mysql_info = debugpress_db()->wpdb()->get_results( 'SHOW VARIABLES' );

		foreach ( $mysql_info as $info ) {
			echo sprintf( '<tr><th>%s:</th><td>%s</td></tr>', $info->Variable_name, debugpress_rs( htmlspecialchars( $info->Value ), false ) );
		}

		?>
        </tbody>
    </table>
</div>