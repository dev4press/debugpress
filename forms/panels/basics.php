<?php

use Dev4Press\Plugin\DebugPress\Panel\Basics;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Basics::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Basics::instance()->right(); ?>
    </div>
</div>
