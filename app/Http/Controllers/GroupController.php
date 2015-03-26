<?php namespace Phonex\Http\Controllers;

use Phonex\Group;
use Phonex\Http\Requests;

class GroupController extends Controller {

	public function __construct(){
	}


	public function index(){
		$groups = Group::paginate(15);
		return view('group.index', compact('groups'));
	}
}
