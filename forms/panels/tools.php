<?php

use Dev4Press\Plugin\DebugPress\Panel\Tools;

?>
<div class="debugpress-grid">
    <div class="debugpress-unit one-third">
		<?php Tools::instance()->left(); ?>
    </div>
    <div class="debugpress-unit two-thirds">
		<?php Tools::instance()->right(); ?>
    </div>
</div>
