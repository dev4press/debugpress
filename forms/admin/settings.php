<div class="wrap">
    <h1><?php _e( "DebugPress Settings", "debugpress" ); ?></h1>

    <form action='options.php' method='post'>
		<?php include( DEBUGPRESS_PLUGIN_PATH . 'forms/admin/sidebar.php' ); ?>
        <div class="debugpress_settings">
			<?php

			settings_fields( 'debugpress' );

			debugpress_do_settings_sections( 'debugpress' );

			submit_button();

			?>
        </div>
    </form>
</div>
