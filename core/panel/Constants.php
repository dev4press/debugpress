<?php

namespace Dev4Press\Plugin\DebugPress\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dev4Press\Plugin\DebugPress\Main\Panel;

class Constants extends Panel {
	private $constants = array(
		'paths' => array(
			'ABSPATH',
			'WPINC',
			'WP_LANG_DIR',
			'WP_PLUGIN_DIR',
			'WP_PLUGIN_URL',
			'WP_CONTENT_DIR',
			'WP_CONTENT_URL',
			'WP_HOME',
			'WP_SITEURL',
			'WP_TEMP_DIR',
			'MUPLUGINDIR',
			'WPMU_PLUGIN_DIR',
			'WPMU_PLUGIN_URL',
		),
		'ms'    => array(
			'MULTISITE',
			'ALLOW_SUBDIRECTORY_INSTALL',
			'BLOG_ID_CURRENT_SITE',
			'DOMAIN_CURRENT_SITE',
			'DIEONDBERROR',
			'ERRORLOGFILE',
			'BLOGUPLOADDIR',
			'NOBLOGREDIRECT',
			'PATH_CURRENT_SITE',
			'UPLOADBLOGSDIR',
			'SITE_ID_CURRENT_SITE',
			'SUBDOMAIN_INSTALL',
			'UPLOADS',
			'WPMU_ACCEL_REDIRECT',
			'WPMU_SENDFILE',
			'WP_ALLOW_MULTISITE',
		),
		'dbg'   => array(
			'WP_DEBUG',
			'WP_DEBUG_DISPLAY',
			'WP_DEBUG_LOG',
			'SAVEQUERIES',
			'SCRIPT_DEBUG',
		),
		'sys'   => array( 'WP_START_TIMESTAMP', 'WP_MAX_MEMORY_LIMIT', 'WP_MEMORY_LIMIT' ),
		'glbl'  => array(
			'SUNRISE',
			'WP_CACHE',
			'COMPRESS_CSS',
			'ENFORCE_GZIP',
			'COMPRESS_SCRIPTS',
			'CONCATENATE_SCRIPTS',
			'WP_POST_REVISIONS',
			'AUTOSAVE_INTERVAL',
			'DISABLE_WP_CRON',
			'EMPTY_TRASH_DAYS',
			'IMAGE_EDIT_OVERWRITE',
			'MEDIA_TRASH',
			'WP_ENVIRONMENT_TYPE',
			'WP_CRON_LOCK_TIMEOUT',
			'WP_MAIL_INTERVAL',
			'WPLANG',
			'SHORTINIT',
			'RANDOM_COMPAT_READ_BUFFER',
			'WP_RECOVERY_MODE_SESSION_ID',
			'CORE_UPGRADE_SKIP_NEW_BUNDLED',
			'DO_NOT_UPGRADE_GLOBAL_TABLES',
			'PO_MAX_LINE_LEN',
			'DIR_TESTDATA',
		),
		'req'   => array(
			'IFRAME_REQUEST',
			'DOING_CRON',
			'DOING_AJAX',
			'REST_REQUEST',
			'DOING_AUTOSAVE',
			'WP_SETUP_CONFIG',
		),
		'bck'   => array( 'POST_BY_EMAIL', 'EDIT_ANY_USER' ),
		'db'    => array(
			'DB_CHARSET',
			'DB_COLLATE',
			'WP_ALLOW_REPAIR',
			'WP_USE_EXT_MYSQL',
			'CUSTOM_USER_TABLE',
			'CUSTOM_USER_META_TABLE',
		),
		'thm'   => array(
			'WP_DEFAULT_THEME',
			'BACKGROUND_IMAGE',
			'HEADER_IMAGE',
			'HEADER_IMAGE_HEIGHT',
			'HEADER_IMAGE_WIDTH',
			'HEADER_TEXTCOLOR',
			'NO_HEADER_TEXT',
			'WP_USE_THEMES',
			'STYLESHEETPATH',
			'TEMPLATEPATH',
		),
		'sec'   => array(
			'ADMIN_COOKIE_PATH',
			'ALLOW_UNFILTERED_UPLOADS',
			'COOKIEHASH',
			'AUTH_COOKIE',
			'LOGGED_IN_COOKIE',
			'PASS_COOKIE',
			'COOKIEPATH',
			'COOKIE_DOMAIN',
			'PLUGINS_COOKIE_PATH',
			'RECOVERY_MODE_COOKIE',
			'SECURE_AUTH_COOKIE',
			'SITECOOKIEPATH',
			'CUSTOM_TAGS',
			'TEST_COOKIE',
			'USER_COOKIE',
			'FORCE_SSL_ADMIN',
			'FORCE_SSL_LOGIN',
			'DISALLOW_UNFILTERED_HTML',
			'DISALLOW_FILE_EDIT',
			'DISALLOW_FILE_MODS',
		),
	);

	public function left() {
		$this->title( esc_html__( 'Path, directories', 'debugpress' ) );
		$this->list_defines( $this->constants['paths'] );

		$this->title( esc_html__( 'Debug', 'debugpress' ) );
		$this->list_defines( $this->constants['dbg'] );

		$this->title( esc_html__( 'System', 'debugpress' ) );
		$this->list_defines( $this->constants['sys'] );

		$this->title( esc_html__( 'Theme', 'debugpress' ) );
		$this->list_defines( $this->constants['thm'] );

		$this->title( esc_html__( 'Database', 'debugpress' ) );
		$this->list_defines( $this->constants['db'] );

		$this->title( esc_html__( 'Request', 'debugpress' ) );
		$this->list_defines( $this->constants['req'] );
	}

	public function right() {
		$this->title( esc_html__( 'Global', 'debugpress' ) );
		$this->list_defines( $this->constants['glbl'] );

		$this->title( esc_html__( 'Multisite', 'debugpress' ) );
		$this->list_defines( $this->constants['ms'] );

		$this->title( esc_html__( 'Security', 'debugpress' ) );
		$this->list_defines( $this->constants['sec'] );

		$this->title( esc_html__( 'Back Compatibility', 'debugpress' ) );
		$this->list_defines( $this->constants['bck'] );
	}
}
