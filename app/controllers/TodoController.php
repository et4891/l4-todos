<?php

/**
 * Class TodoController
 * METHODS
 * -getIndex()
 * -add()
 * -search()
 */
class TodoController extends BaseController {

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

		//Insert into the regular todo table
		$todo = Todo::create(array(
			'todoText' 	=> $todoTextInput,
			'created_at' => $dateValueWithTimeStamp,
			'done' 		=> 0
		));

		if($todo){
			$todo->save();
		}
		//Done saving into the regular todo table

		/**************************************/
		/***********List Back Up***************/
		/**************************************/
		$todoBackup = TodoBackUp::create(array(
			'todoText' 	=> $todoTextInput,
			'created_at' => $dateValueWithTimeStamp,
			'done' 		=> 0
		));

		if($todoBackup){
			$todoBackup->save();
		}
		/**************************************/

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
				$data[$index]['done'] = $todo['done'];
				$data[$index]['id']   = $todo['id'];
				$index++;
			}
		}
		return json_encode($data);
	}

	/**
	 *  calls the doubleD() and runs the done action
     */
	public function done(){
		echo $this->doubleD('done');
	}

	/**
	 * calls the doubleD() and runs the delete action
     */
	public function delete(){
		echo $this->doubleD('delete');
	}

	/**
	 * Used in done() and delete()
	 * The specific row is determined by using the id and the todoText
	 * If both of them matches the row in the database, that is the row needed.
	 *
	 * When action done is passed into the parameter, the database will change the row's done column to 1 which means the item is done
	 * When action delete is passed into the parameter, the database will delete the row.
	 *
	 * Named doubleD because done and delete both start with d
	 * @param $action			-either done or delete to determine the action will be done in the database
	 * @return string			-just a string to return saying done / delete success
     */
	public function doubleD($action){
		$id = $_POST['getID'];
		$todoText = htmlentities($_POST['todoText']);

		if($action == 'done'){
			$todos = Todo::where('id', '=', $id)->where('todoText', '=', $todoText)->get();
			$todos[0]->done = 1;
			$todos[0]->touch();
			$todos[0]->save();

			/**************************************/
			/*******Done*List Back Up**************/
			/**************************************/
			$todoBackup = TodoBackUp::where('id', '=', $id)->where('todoText', '=', $todoText)->get();
			$todoBackup[0]->done = 1;
			$todoBackup[0]->touch();
			$todoBackup[0]->save();
			/**************************************/
			/**************************************/

			return json_encode('done-success');
		}

		if($action == 'delete'){
			$todos = Todo::where('id', '=', $id)->where('todoText', '=', $todoText)
				->where('done', '=', 1)->get();
			$todos[0]->delete();

			return json_encode('delete-success');
		}

	}
}
