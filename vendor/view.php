<?php

namespace Goat\backend;
use Goat\goat;
class View {

	private $template;
    //private $assets = [];
	public function __construct($template)
	{
		$this->template = realpath(APP_DIR .'views/'. $template .'.php');
	}

	public function render($params = [])
	{
		extract($params);
		ob_start();
		require($this->template);
		echo ob_get_clean();
	}
}