<?php

use Dev4Press\Plugin\DebugPress\Panel\Content;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
        <?php Content::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
        <?php Content::instance()->right(); ?>
    </div>
</div>
