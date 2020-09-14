<?php

use Dev4Press\Plugin\DebugPress\Panel\System;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php System::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php System::instance()->right(); ?>
    </div>
</div>