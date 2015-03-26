<?php namespace Phonex\Http\Controllers;

use Phonex\Group;
use Phonex\Http\Requests;

class GroupController extends Controller {

	public function __construct(){
	}


	public function index(){
		$users = Group::all();
        dd($users);

//        if(InputGet::has('username')){
//            $query = $query->where('username', 'LIKE', "%" . InputGet::getAlphaNum('username') . "%");
//        }

//        $users = $query->paginate($limit);
		return view('group.index', compact('users'));
	}
}
