<?php

use Dev4Press\Plugin\DebugPress\Panel\Errors;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
		<?php Errors::instance()->single(); ?>
    </div>
</div>
