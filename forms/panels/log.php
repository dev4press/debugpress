<?php

use Dev4Press\Plugin\DebugPress\Panel\Log;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
		<?php Log::instance()->single(); ?>
    </div>
</div>
