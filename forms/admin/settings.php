<div class="wrap debugpress-panel debugpress-panel-settings">
    <h1><?php _e( "DebugPress Settings", "debugpress" ); ?></h1>

    <nav class="nav-tab-wrapper">
        <a href="#activation" class="nav-tab nav-tab-change nav-tab-active"><?php _e( "Activation", "debugpress" ); ?></a>
        <a href="#panels" class="nav-tab nav-tab-change"><?php _e( "Panel", "debugpress" ); ?></a>
        <a href="#advanced" class="nav-tab nav-tab-change"><?php _e( "Advanced", "debugpress" ); ?></a>
        <a href="https://debug.press" target="_blank" rel="noopener nofollow" class="nav-tab nav-tab-right"><span class="dashicons dashicons-external"></span><?php _e( "Website", "debugpress" ); ?>
        </a>
        <a href="tools.php?page=debugpress-info" class="nav-tab nav-tab-right"><span class="dashicons dashicons-admin-tools"></span><?php _e( "Tools", "debugpress" ); ?>
        </a>
    </nav>

    <form action='options.php' method='post'>
        <div class="debugpress_settings">
			<?php

			settings_fields( 'debugpress' );

			?>

			<?php

			debugpress_do_settings_sections( 'debugpress' );

			submit_button();

			?>
        </div>
		<?php include( DEBUGPRESS_PLUGIN_PATH . 'forms/admin/sidebar.php' ); ?>
    </form>
</div>
