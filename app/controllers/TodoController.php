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
	 * Date chosen or not, a date comparison will happen.
	 * If a date is not chosen, what is entered will be added into today's date and current time
	 * If a date is chosen, then what is entered will be added to the date chosen with current time
	 * This will only be active if date is not chosen,which by default will be current date/time, or if the date selected is in the future.
	 * If date chosen is in the past and somehow add button is clicked (which shouldn't happen) a boolean will be passed to the client side and javascript alert will pop up.
	 * After adding, it'll also print a list of the todoText
     */
	public function add(){
		$todoTextInput = htmlentities(Input::get('todoTextInput'));
		if($_POST['dateValue'] == ''){
			$dateValue = date('Y-m-d');
		}else{
			$dateValue = $_POST['dateValue'];
		}

		$todayDate = (new DateTime())->format('Y-m-d');					//Get today's date
		$selectDate = (new DateTime($dateValue))->format('Y-m-d');		//Get selected date

		$boolean = (strtotime($todayDate) <= strtotime($selectDate));	//Boolean comparison, is selecteDate bigger or equal than todayDate?
		if($boolean){
			$dateValueWithTimeStamp = date($dateValue . ' H:i:s');		//Not needed anymore but kept on case if the need in the future

            $this->addQuery('Todo', $todoTextInput, $dateValue);
            $this->addQuery('TodoBackUp', $todoTextInput, $dateValue);

			echo $this->insertDateValue($dateValue);
		}else{
			$data = $boolean;
			echo json_encode($data);
		}
	}

    /**
     * Used in add()
     * Query to save todoText in database
     * @param $table                    - Name of the table to query
     * @param $todoTextInput            - The input for todoText
     * @param $dateValue   				- Date this item is created for
     */
    public function addQuery($table, $todoTextInput, $dateValue){
        $query = $table::create(array(
            'todoText' 	=> $todoTextInput,
            'created_for' => $dateValue,
            'done' 		=> 0
        ));

        if($query){
            $query->save();
        }
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
		$todos = Todo::where('created_for', 'LIKE',  $dateValue.'%')->get();
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
	 * The specific row is determined by using the id, the todoText and the done
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
			$this->doubleDQuery('Todo', $id, $todoText, 0, $action);
			$this->doubleDQuery('TodoBackUp', $id, $todoText, 0, $action);
			return json_encode('done-success');
		}

		if($action == 'delete'){
			$this->doubleDQuery('Todo', $id, $todoText, 1, $action);
			return json_encode('delete-success');
		}

	}

	/**
	 * Used in doubleD()
	 * A function for querying the database since this is done repeatedly few times in doubleD()
	 * @param $table			- Name of the table to query
	 * @param $id				- Match the id in the table while querying
	 * @param $todoText			- Match the todoText in the table while querying
	 * @param $done				- Match the done in the table while querying
	 * @param $action			- If this function is used for done then query will change done column to 1, update the update_at column and saves
	 *							- If this function is used for delete then it'll delete the row in database
     */
	public function doubleDQuery($table, $id, $todoText, $done, $action){
		$query = $table::where('id', '=', $id)->where('todoText', '=', $todoText)
			->where('done', '=', $done)->first();
		if($action == 'done'){
			$query->done = 1;
			$query->touch();
			$query->save();
		}

		if($action == 'delete'){
			$query->delete();
		}

	}

}
