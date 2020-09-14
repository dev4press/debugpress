<?php

use Dev4Press\Plugin\DebugPress\Panel\Request;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Request::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Request::instance()->right(); ?>
    </div>
</div>