<div class="wrap debugpress-panel debugpress-panel-settings">
    <h1><?php esc_html_e( 'DebugPress Settings', 'debugpress' ); ?></h1>

    <nav class="nav-tab-wrapper">
        <a href="#activation" class="nav-tab nav-tab-change nav-tab-active">
			<?php esc_html_e( 'Activation', 'debugpress' ); ?>
        </a>
        <a href="#panels" class="nav-tab nav-tab-change">
			<?php esc_html_e( 'Panels', 'debugpress' ); ?>
        </a>
        <a href="#tracking" class="nav-tab nav-tab-change">
			<?php esc_html_e( 'Tracking', 'debugpress' ); ?>
        </a>
        <a href="#advanced" class="nav-tab nav-tab-change">
			<?php esc_html_e( 'Advanced', 'debugpress' ); ?>
        </a>
        <a href="https://debug.press" target="_blank" rel="noopener nofollow" class="nav-tab nav-tab-right">
            <span class="dashicons dashicons-external"></span><?php esc_html_e( 'Website', 'debugpress' ); ?>
        </a>
        <a href="tools.php?page=debugpress-info" class="nav-tab nav-tab-right">
            <span class="dashicons dashicons-admin-tools"></span><?php esc_html_e( 'Tools', 'debugpress' ); ?>
        </a>
    </nav>

    <form action='options.php' method='post'>
        <div class="debugpress_settings">
			<?php

			settings_fields( 'debugpress' );

			debugpress_do_settings_sections( 'debugpress' );

			submit_button();

			?>
        </div>
		<?php include DEBUGPRESS_PLUGIN_PATH . 'forms/admin/sidebar.php'; ?>
    </form>
</div>
