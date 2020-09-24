<?php

use Dev4Press\Plugin\DebugPress\Panel\Store;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
		<?php Store::instance()->single(); ?>
    </div>
</div>
