<?php

use Dev4Press\Plugin\DebugPress\Panel\HTTP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
        <?php HTTP::instance()->single(); ?>
    </div>
</div>
