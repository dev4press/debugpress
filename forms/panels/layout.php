<?php

use Dev4Press\Plugin\DebugPress\Panel\Layout;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
        <?php Layout::instance()->single(); ?>
    </div>
</div>
