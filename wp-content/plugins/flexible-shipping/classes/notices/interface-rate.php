<?php

namespace WPDesk\FS\Rate;

interface RateNoticeInterface  {


	/**
	 * Show message
	 *
	 * @return string
	 */
	public function show_message();


	/**
	 * Should show message
	 *
	 * @return bool
	 */
	public function should_show_message();

}
