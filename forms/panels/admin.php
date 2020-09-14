<?php use Dev4Press\Plugin\DebugPress\Panel\Admin; ?>

<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Admin::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Admin::instance()->right(); ?>
    </div>
</div>