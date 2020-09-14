<?php

use Dev4Press\Plugin\DebugPress\Display\Loader;

?>
<div id="gdpet-debugger-container" style="display: none;">
    <div id="gdpet-debugger-content-header">
        <ul role="tablist" id="gdpet-debugger-tabs" class="debugpress-clearfix">
			<?php
			$first = true;
			foreach ( Loader::instance()->tabs as $tab => $label ) {
				echo '<li role="presentation" id="gdpet-debugger-tab-' . $tab . '-li" class="' . ( $first ? 'gdpet-tab-active' : '' ) . '">';
				echo '<a tabindex="0" role="tab" aria-controls="gdpet-debugger-tab-' . $tab . '" href="#gdpet-debugger-tab-' . $tab . '"' . ( $first ? ' aria-selected="true"' : '' ) . '>' . $label . '</a>';
				echo '</li>';

				$first = false;
			}

			?>
        </ul>
        <select id="gdpet-debugger-select" aria-label="<?php _e( "Select Debugger Panel", "debugpress" ); ?>">
			<?php

			$first = true;
			foreach ( Loader::instance()->tabs as $tab => $label ) {
				echo '<option value="gdpet-debugger-tab-' . $tab . '"' . ( $first ? ' selected="selected"' : '' ) . '>' . $label . '</option>';

				$first = false;
			}

			?>
        </select>
    </div>
    <div id="gdpet-debugger-content-wrapper">
		<?php

		$first = true;
		foreach ( Loader::instance()->tabs as $tab => $label ) {
			echo '<div id="gdpet-debugger-tab-' . $tab . '" role="tabpanel" class="gdpet-tab-content ' . ( $first ? 'gdpet-tab-active' : '' ) . '"' . ( ! $first ? ' aria-hidden="true"' : '' ) . '>';

			$panel_path = apply_filters( 'debugpress_debugger_panel_path_' . $tab, DEBUGPRESS_PLUGIN_PATH . 'forms/panels/' . $tab . '.php' );

			if ( file_exists( $panel_path ) ) {
				include( $panel_path );
			}

			echo '</div>';

			$first = false;
		}

		?>
    </div>
    <div id="gdpet-debugger-content-footer" class="debugpress-clearfix">
        <div class="gdpet-debugger-footer-left">
			<?php echo debugpress_plugin()->build_stats( null ); ?>
        </div>
        <div class="gdpet-debugger-footer-right">
            <a target="_blank" href="<?php echo admin_url( 'options-general.php?page=debugpress' ); ?>"><?php _e( "Settings", "debugpress" ); ?></a>
            &middot; <a target="_blank" href="https://debug.press/"><?php _e( "DebugPress", "debugpress" ); ?></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        window.wp.dev4press.debugpress.init(<?php echo json_encode( debugpress_tracker()->counts ); ?>, <?php echo debugpress_plugin()->get( 'ajax' ) ? 'true' : 'false'; ?>);
    });
</script>
