<div id="content">
<div id="messages">
<?php
	echo validation_errors();
?>
</div>
<div id="inner-content">
<?php $this->load->view($view, $data); ?>
</div>
</div>
