<?php
namespace Goat\backend;
class Controller{
	private $_behaviors;
	public $app;
	public $forwarded;

	public function __construct()
	{

		$this->includeBehaviors();

		$this->_behaviors = $this->behaviors();
		if(!empty($this->_behaviors))
		{
			$bManager = new Behavior();
			$bManager->behaviors = $this->_behaviors;
			$bManager->initiate();
		}
	}
	public function behaviors()
	{
		return [];
	}

	public function __call($name, $arguments)
	{
		foreach($this->forwarded as $forwardedFunction){
			if($name == $forwardedFunction){
				return call_user_func_array([$this->app, $name], $arguments);
			}
		}
		return null;
	}

	/*Begin of protected functions*/
	final private function includeBehaviors()
	{
		foreach (glob(APP_DIR . "behaviors/" . "*.php") as $filename) {
			require_once $filename;
		}
	}
}
