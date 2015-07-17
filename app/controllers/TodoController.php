<?php

/**
 * Class TodoController
 * METHODS
 * -getIndex()
 * -add()
 * -search()
 */
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

	/**
	 * @return direct to todo.blade.php page
     */
	public function getIndex(){
		return View::make('todo');
	}

	public function add(){
		$data = array();
		$todoTextInput = htmlentities(Input::get('todoTextInput'));
		$todo = Todo::create(array(
			'todoText' 	=> $todoTextInput,
			'done' 		=> 0
		));

		if($todo){
			$todo->save();
			$data[] = 'Successed added';
		}else{
			$data[] = 'Adding failed';
		}
		echo json_encode($data);
	}

	/**
	 * @return string
	 * Search todoText in database using the yy-mm-dd format from the value passed here from the client side
	 * Pass the todoText as json back to client side
     */
	public function search(){
		$data = array();
		$dateValue = $_POST['dateValue'];
		$index = 0;

		$todos = Todo::where('created_at', 'LIKE',  $dateValue.'%')->get();
		if(sizeof($todos) == 0){
			$data[$index] = 'empty';
		}else{
			foreach($todos as $key => $todo){
				$data[$index]['todo'] = $todo['todoText'];
				$index++;
			}
		}
		return json_encode($data);
	}

}
