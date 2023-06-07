<div class="debugpress_info">
    <table>
        <thead>
        <tr>
            <th><?php esc_html_e( "Variable", "debugpress" ); ?></th>
            <th><?php esc_html_e( "Value", "debugpress" ); ?></th>
        </tr>
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