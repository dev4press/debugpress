<?php

use Dev4Press\Plugin\DebugPress\Display\Loader;

?>
<div id="debugpress-debugger-container" style="display: none;">
    <div id="debugpress-debugger-content-header">
        <ul role="tablist" id="debugpress-debugger-tabs" class="debugpress-clearfix">
			<?php

			$first = true;
			foreach ( Loader::instance()->tabs as $_tab => $obj ) {
				$label   = is_array( $obj ) ? ( $obj['tab'] ?? $obj['label'] ) : $obj;
				$icon    = is_array( $obj ) ? ( $obj['icon'] ?? '' ) : 'bug';
				$counter = is_array( $obj ) && ( ( $obj['counter'] ?? false ) );
				$_title  = is_array( $obj ) ? ' title="' . $obj['label'] . '"' : '';

				if ( ! empty( $icon ) ) {
					$label = '<i class="debugpress-icon debugpress-icon-' . $icon . ' debugpress-tab-ctrl-icon"></i><span class="debugpress-tab-ctrl-span">' . $label . '</span>';
				}

				if ( $counter ) {
					$label .= ' (<span class="debugpress-counter">0</span>)';
				}

				echo '<li role="presentation" id="debugpress-debugger-tab-' . esc_attr( $_tab ) . '-li" class="' . ( $first ? 'debugpress-tab-active' : '' ) . '">';
				echo '<a tabindex="0" role="tab" aria-controls="debugpress-debugger-tab-' . esc_attr( $_tab ) . '" href="#debugpress-debugger-tab-' . esc_attr( $_tab ) . '"' . ( $first ? ' aria-selected="true"' : '' ) . $_title . '>' . $label . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
				echo '</li>';

				$first = false;
			}

			?>
        </ul>
        <select id="debugpress-debugger-select" aria-label="<?php esc_html_e( 'Select Debugger Panel', 'debugpress' ); ?>">
			<?php

			$first = true;
			foreach ( Loader::instance()->tabs as $_tab => $obj ) {
				$label = is_array( $obj ) ? $obj['label'] : $obj;

				echo '<option value="debugpress-debugger-tab-' . esc_attr( $_tab ) . '"' . ( $first ? ' selected="selected"' : '' ) . '>' . esc_attr( $label ) . '</option>';

				$first = false;
			}

			?>
        </select>
    </div>
    <div id="debugpress-debugger-content-wrapper">
		<?php

		$first = true;
		foreach ( Loader::instance()->tabs as $_tab => $label ) {
			echo '<div id="debugpress-debugger-tab-' . esc_attr( $_tab ) . '" role="tabpanel" class="debugpress-tab-content ' . ( $first ? 'debugpress-tab-active' : '' ) . '"' . ( ! $first ? ' aria-hidden="true"' : '' ) . '>';

			$panel_path = apply_filters( 'debugpress-debugger-panel-path-' . esc_attr( $_tab ), DEBUGPRESS_PLUGIN_PATH . 'forms/panels/' . $_tab . '.php' );

			if ( file_exists( $panel_path ) ) {
				include $panel_path;
			}

			echo '</div>';

			$first = false;
		}

		?>
    </div>
    <div id="debugpress-debugger-content-footer" class="debugpress-clearfix">
        <div class="debugpress-debugger-footer-left">
			<?php echo debugpress_plugin()->build_stats( null ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>
        <div class="debugpress-debugger-footer-right">
            <a target="_blank" href="<?php echo esc_url( admin_url( 'options-general.php?page=debugpress' ) ); ?>"><?php esc_html_e( 'Settings', 'debugpress' ); ?></a>
            &middot;
            <a rel="noopener" target="_blank" href="https://debug.press/"><?php esc_html_e( 'DebugPress', 'debugpress' ); ?></a>
            <strong>v<?php echo esc_html( DEBUGPRESS_VERSION ); ?></strong>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        window.wp.dev4press.debugpress.init(
			<?php echo wp_json_encode( debugpress_tracker()->get_counts_js() ); ?>,
			<?php echo wp_json_encode( debugpress_tracker()->get_stats() ); ?>,
			<?php echo debugpress_plugin()->get( 'ajax' ) ? 'true' : 'false'; ?>,
			<?php echo is_admin() ? 'true' : 'false'; ?>);
    });
</script>
