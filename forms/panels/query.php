<?php

use Dev4Press\Plugin\DebugPress\Panel\Query;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Query::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Query::instance()->right(); ?>
    </div>
</div>