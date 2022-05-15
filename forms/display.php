<?php

use Dev4Press\Plugin\DebugPress\Display\Loader;

?>
<div id="debugpress-debugger-container" style="display: none;">
    <div id="debugpress-debugger-content-header">
        <ul role="tablist" id="debugpress-debugger-tabs" class="debugpress-clearfix">
			<?php

			$first = true;
			foreach ( Loader::instance()->tabs as $tab => $obj ) {
				$label = is_array( $obj ) ? $obj['tab'] : $obj;
				$title = is_array( $obj ) ? ' title="' . $obj['label'] . '"' : '';

				echo '<li role="presentation" id="debugpress-debugger-tab-' . $tab . '-li" class="' . ( $first ? 'debugpress-tab-active' : '' ) . '">';
				echo '<a tabindex="0" role="tab" aria-controls="debugpress-debugger-tab-' . $tab . '" href="#debugpress-debugger-tab-' . $tab . '"' . ( $first ? ' aria-selected="true"' : '' ) . $title . '>' . $label . '</a>';
				echo '</li>';

				$first = false;
			}

			?>
        </ul>
        <select id="debugpress-debugger-select" aria-label="<?php _e( "Select Debugger Panel", "debugpress" ); ?>">
			<?php

			$first = true;
			foreach ( Loader::instance()->tabs as $tab => $obj ) {
				$label = is_array( $obj ) ? $obj['label'] : $obj;

				echo '<option value="debugpress-debugger-tab-' . $tab . '"' . ( $first ? ' selected="selected"' : '' ) . '>' . $label . '</option>';

				$first = false;
			}

			?>
        </select>
    </div>
    <div id="debugpress-debugger-content-wrapper">
		<?php

		$first = true;
		foreach ( Loader::instance()->tabs as $tab => $label ) {
			echo '<div id="debugpress-debugger-tab-' . $tab . '" role="tabpanel" class="debugpress-tab-content ' . ( $first ? 'debugpress-tab-active' : '' ) . '"' . ( ! $first ? ' aria-hidden="true"' : '' ) . '>';

			$panel_path = apply_filters( 'debugpress-debugger-panel-path-' . $tab, DEBUGPRESS_PLUGIN_PATH . 'forms/panels/' . $tab . '.php' );

			if ( file_exists( $panel_path ) ) {
				include( $panel_path );
			}

			echo '</div>';

			$first = false;
		}

		?>
    </div>
    <div id="debugpress-debugger-content-footer" class="debugpress-clearfix">
        <div class="debugpress-debugger-footer-left">
			<?php echo debugpress_plugin()->build_stats( null ); ?>
        </div>
        <div class="debugpress-debugger-footer-right">
            <a target="_blank" href="<?php echo admin_url( 'tools.php?page=debugpress-info' ); ?>"><?php _e( "Tools", "debugpress" ); ?></a>
            &middot;
            <a target="_blank" href="<?php echo admin_url( 'options-general.php?page=debugpress' ); ?>"><?php _e( "Settings", "debugpress" ); ?></a>
            &middot;
            <a rel="noopener" target="_blank" href="https://debug.press/"><?php _e( "DebugPress", "debugpress" ); ?></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        window.wp.dev4press.debugpress.init(<?php echo json_encode( debugpress_tracker()->counts ); ?>, <?php echo debugpress_plugin()->get( 'ajax' ) ? 'true' : 'false'; ?>);
    });
</script>
