<div class="wrap">
    <h1><?php _e("DebugPress Settings"); ?></h1>

    <form action='options.php' method='post'>
        <?php

        settings_fields( 'debugpress' );

        do_settings_sections( 'debugpress' );

        submit_button();

        ?>
    </form>
</div>