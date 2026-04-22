<?php

use Dev4Press\Plugin\DebugPress\Panel\Deprecated;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
        <?php Deprecated::instance()->single(); ?>
    </div>
</div>