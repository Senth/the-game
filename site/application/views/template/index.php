<?php
echo $this->load->view('template/header');
// Only display sidebar for logged in teams
if ($this->user_info->is_logged_in()) {
	echo $this->load->view('template/sidebar');
}
echo $this->load->view('template/content', $inner_content);
echo $this->load->view('template/footer');
?>
