<?php

$mysql_info = debugpress_db()->wpdb()->get_results( 'SHOW VARIABLES' );

?>
<div class="debugpress_info">
    <table>
        <thead>
        <tr>
            <th><?php esc_html_e( 'Variable', 'debugpress' ); ?></th>
            <th><?php esc_html_e( 'Value', 'debugpress' ); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php

		if ( is_array( $mysql_info ) && ! empty( $mysql_info ) ) {
			foreach ( $mysql_info as $info ) {
				printf( '<tr><th>%s:</th><td>%s</td></tr>', $info->Variable_name, debugpress_rs( htmlspecialchars( $info->Value ), false ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		} else {
			printf( '<tr><th colspan="2">%s</th></tr>', esc_attr__( 'Data Not Available.', 'debugpress' ) );
		}

		?>
        </tbody>
    </table>
</div>