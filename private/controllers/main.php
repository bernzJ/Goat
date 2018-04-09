<?php
use Goat\goat,Goat\backend\Controller;
class Main extends Controller {

	public function behaviors()
	{
		return [
			[
				'className' => \Goat\backend\accessBehavior::className(),
				'properties' => [
					'isLogged' => true,
					'user' => 'all',
				],
				'functions' => [
					'init'
				],
			]
		];
	}


	function index()
	{

		$template = $this->loadView('main_view');
		$r = $this->route();
		//$headerview = $this->loadView('header')->render(["route" => (strstr($r, '?')) ? explode('?', $r)[0] : $r ]);
		//update bots
		//$_bots = $this->loadModel("bots");
		//$_bots->updateBots();

		$template->render();


	}
    
}
