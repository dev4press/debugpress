<?php

use Dev4Press\Plugin\DebugPress\Panel\DebugLog;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit one-quarter">
		<?php DebugLog::instance()->left(); ?>
    </div>
    <div class="debugpress-unit three-quarters">
		<?php DebugLog::instance()->right(); ?>
    </div>
</div>
