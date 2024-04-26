<?php

use Dev4Press\Plugin\DebugPress\Main\OPCache;

$_tabs = apply_filters( 'debugpress-tools-tabs', array(
	'php'     => __( 'PHP Info', 'debugpress' ),
	'opcache' => __( 'OPCache Info', 'debugpress' ),
	'mysql'   => __( 'MySQL Variables', 'debugpress' ),
) );

if ( ! OPCache::instance()->has_opcache() ) {
	unset( $_tabs['opcache'] );
}

$_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$_tab = ! isset( $_tabs[ $_tab ] ) ? '' : $_tab;

?>

<div class="wrap debugpress-panel debugpress-panel-tools">
    <h1><?php esc_html_e( 'DebugPress Information', 'debugpress' ); ?></h1>

    <nav class="nav-tab-wrapper">
        <a href="<?php echo esc_url( admin_url( "tools.php?page=debugpress-info" ) ); ?>" class="nav-tab<?php echo empty( $_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Intro', 'debugpress' ); ?></a>

		<?php foreach ( $_tabs as $t => $label ) { ?>
            <a href="<?php echo esc_url( admin_url( "tools.php?page=debugpress-info&tab=" . $t ) ); ?>" class="nav-tab<?php echo $_tab == $t ? ' nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
		<?php } ?>

        <a href="<?php echo esc_url( admin_url( "options-general.php?page=debugpress" ) ); ?>" class="nav-tab nav-tab-right"><span class="dashicons dashicons-admin-settings"></span><?php esc_html_e( 'Settings', 'debugpress' ); ?>
        </a>
    </nav>

    <div class="tab-content">
		<?php

		$file = empty( $_tab ) ? 'info' : $_tab;
		$file = DEBUGPRESS_PLUGIN_PATH . 'forms/tools/' . $file . '.php';
		$file = apply_filters( 'debugpress-tools-tab-file-' . $_tab, $file );

		require $file;

		?>
    </div>
</div>
