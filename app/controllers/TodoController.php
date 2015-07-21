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
		$date = date('Y-m-d H:i:s');
		return View::make('todo')->with('date', $date);
	}

	/**
	 * Adds what is typed in the input field into database
	 * If a date is not chosen, what is entered will be added into today's date and current time
	 * If a date is chosen, then what is entered will be added to the date chosen with current time
	 * After adding, it'll also print a list of the todoText
     */
	public function add(){
		$todoTextInput = htmlentities(Input::get('todoTextInput'));
		if($_POST['dateValue'] == ''){
			$dateValue = date('Y-m-d');
		}else{
			$dateValue = $_POST['dateValue'];
		}

		$dateValueWithTimeStamp = date($dateValue . ' H:i:s');

		$todo = Todo::create(array(
			'todoText' 	=> $todoTextInput,
			'created_at' => $dateValueWithTimeStamp,
			'done' 		=> 0
		));

		if($todo){
			$todo->save();
		}

		echo $this->insertDateValue($dateValue);
	}

	/**
	 * Simply uses the dateValue in the format of yy-mm-dd to pull the todo list out of the database
     */
	public function search(){
		echo $this->insertDateValue($_POST['dateValue']);
	}


	/**
	 * **NOT A NECESSARY NEEDED FUNCTION**
	 * To enter the date in - yy-mm-dd and pass it to the printTodoList()
	 * @param $dateValue - yy-mm-dd if null then will set as today
	 * @return string    - whatever that is in printTodoList()
	 */
	public function insertDateValue($dateValue){
		return $this->printTodoList($dateValue);
	}

	/**
	 * To search the todo list entered into the database and pass them back to the view
	 * This method is currently used inside insertDateValue()
	 * @param $dateValue - yy-mm-dd passed into here to find the todo list of the day in database
	 * @return string	 - return all the todoList in json format
	 */
	public function printTodoList($dateValue){
		$data = array();
		$index = 0;

		//Use the dateValue (yy-mm-dd) to find all rows with the same dateValue and ignores the time
		//If nothing can be found, a string of empty will be returned or else the todoText in the database will be returned
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
