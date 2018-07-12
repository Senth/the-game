<div id="debug_message"></div>
<div id="content">
<div id="messages">
<?php
	echo get_success();
	echo get_errors();
	echo validation_errors();
?>
</div>
<div id="inner-content">
<?php $this->load->view($view, $data); ?>
</div>
</div>
