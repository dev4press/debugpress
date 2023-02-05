<?php

use Dev4Press\Plugin\DebugPress\Panel\Rewriter;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Rewriter::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Rewriter::instance()->right(); ?>
    </div>
</div>
