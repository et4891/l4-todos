<?php

class TodoController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function getIndex(){
		return View::make('todo');
	}

	public function search(){
		$data = array();
		$dateValue = $_POST['dateValue'];

		$todos = Todo::where('created_at', 'LIKE',  $dateValue.'%')->get();
		if(sizeof($todos) == 0){
			$todos = 'empty';
		}else{
			var_dump($todos);die;
		}
		echo json_encode($todos);
	}

}
