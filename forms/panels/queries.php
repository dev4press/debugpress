<?php

use Dev4Press\Plugin\DebugPress\Panel\Queries;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit one-quarter">
		<?php Queries::instance()->left(); ?>
    </div>
    <div class="debugpress-unit three-quarters">
		<?php Queries::instance()->right(); ?>
    </div>
</div>