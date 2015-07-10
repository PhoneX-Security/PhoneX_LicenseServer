<?php namespace Phonex\Http\Controllers;

use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateGroupRequest;
use Phonex\User;
use Redirect;

class GroupController extends Controller {

	public function __construct(){
	}

	public function index(){
		$groups = Group::paginate(15);
		return view('group.index', compact('groups'));
	}

    public function create()
    {
        return view('group.create');
    }

    public function store(CreateGroupRequest $request)
    {
        $owner = User::findByUsername($request->get('owner_username'));

        $group = new Group();
        $group->name = $request->get('name');
        $group->owner_id = $owner->id;
        $group->comment= $request->get('comment');
        $group->save();

        return Redirect::route('groups.index')
            ->with('success', 'New group ' . $group->name . ' has been created.');
    }
}
