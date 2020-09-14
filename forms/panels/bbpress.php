<?php

use Dev4Press\Plugin\DebugPress\Panel\bbPress;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php bbPress::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php bbPress::instance()->right(); ?>
    </div>
</div>
