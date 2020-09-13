<div class="wrap">
    <div class="debugpress_settings">
        <h1><?php _e( "DebugPress Settings", "debugpress" ); ?></h1>

        <form action='options.php' method='post'>
            <?php

            settings_fields( 'debugpress' );

            do_settings_sections( 'debugpress' );

            submit_button();

            ?>
        </form>
    </div>
    <?php include(DEBUGPRESS_PLUGIN_PATH.'forms/admin/sidebar.php'); ?>
</div>