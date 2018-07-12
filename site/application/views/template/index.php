<?php
$this->load->view('template/header');
// Only display sidebar for logged in teams
if ($team_info->is_logged_in() || $user_info->is_logged_in()) {
	$this->load->view('template/sidebar');
}
$this->load->view('template/content', $inner_content);
$this->load->view('template/footer');
?>
