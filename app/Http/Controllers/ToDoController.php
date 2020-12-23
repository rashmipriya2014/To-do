<?php

namespace App\Http\Controllers;
use App\ToDoList;
use Illuminate\Http\Request;

class ToDoController extends Controller
{   

	/**	 
	 *  Show all incomplete to do
	 */
	public function showAllIncompleteToDos(){
		$all_to_do =  ToDoList::where('is_done',0)->get();
		return view('welcome',['all_to_do' => $all_to_do]);
	}


	/**	 
	 *  Creating record
	 */
    public function addToDo(Request $request){
		
		$status = 0;
		$to_do = $request->todo;
		## Add record
		$add = ToDoList::create(['to_do'=>$to_do]);
		
		if($add){
			$status = 1;
		}
		
		return response()->json(array('status'=> $status), 200);		
	}

	/**	 
	 * Validating todo with database regarding duplicacy 
	 */

	public function validateDuplicacy(Request $request){
		$status = 0;
		$to_do = $request->todo;

		$count_similar_todo = ToDoList::where('to_do',$to_do)->count();

		if($count_similar_todo >= 1){
			## Found Duplicate Record 
			$status = 1;
		}

		return response()->json(array('status'=> $status), 200);
	}

	/**	 
	 * Update to do as done in database
	 */
	public function updateToDoAsDone(Request $request){
		$status = 0;
		$id = $request->id;

		## Update record 
		$update_data = ToDoList::where('id',$id)->update(['is_done'=>1]);
		if($update_data){
			$status = 1;
		}

		return response()->json(array('status'=> $status), 200);
	}

	/**	 
	 * Delete to do from database
	 */
	public function deleteToDo(Request $request){
		$status = 0;
		$id = $request->id;

		## Delete record 
		$delete_record = ToDoList::where('id',$id)->delete();
		if($delete_record){
			$status = 1;
		}

		return response()->json(array('status'=> $status), 200);
	}

	/**	 
	 * Get Details
	 */
	public function getDetails(Request $request){
		$type = $request->type;
		if($type == 'show_all'){
			$toDoList = ToDoList::get();
		}else{
			$toDoList = ToDoList::where('is_done',0)->get();
		}
		
		return $toDoList;
		
	}
}
