<?php

use Dev4Press\Plugin\DebugPress\Panel\HTTP;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit full">
		<?php HTTP::instance()->single(); ?>
    </div>
</div>
