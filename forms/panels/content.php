<?php

use Dev4Press\Plugin\DebugPress\Panel\Content;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit half">
		<?php Content::instance()->left(); ?>
    </div>
    <div class="debugpress-unit half">
		<?php Content::instance()->right(); ?>
    </div>
</div>
