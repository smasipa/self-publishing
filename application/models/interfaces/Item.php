<?php
interface Item{
	public function get_name();
	public function get_db_name();
	public function ban();
	public function unban();
	public function get_url();
}
?>