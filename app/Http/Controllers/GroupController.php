<?php namespace Phonex\Http\Controllers;

use Exception;
use Illuminate\Http\Exception\HttpResponseException;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateGroupRequest;
use Phonex\Http\Requests\UpdateGroupRequest;
use Phonex\User;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function show($id)
    {
        $group = Group::findOrFail($id);
        return view('group.show-details', compact('group'));
    }

    public function showUsers($id)
    {
        $group = Group::with('users')->findOrFail($id);
        return view('group.show-users', compact('group'));
    }

    public function showCodes($id)
    {
        $group = Group::with('exports')->findOrFail($id);
        return view('group.show-bcodes', compact('group'));
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('group.edit', compact('group'));
    }

    public function update($id, UpdateGroupRequest $request)
    {
        $group = Group::findOrFail($id);
//        try {
            $this->validate($request,
                [
                    'name' => 'required|min:5|max:255|unique:groups,name,' . $group->id
                ]);

//        } catch (Exception $e) {
//            dd('sakafaka');
//        }

        $user = User::findByUsername($request->get('owner_username'));

        $group->name = $request->get('name');
        $group->comment = $request->get('comment');
        $group->owner_id = $user->id;
        $group->save();

        return Redirect::route('groups.show', [$group->id])
            ->with('success', 'Group has been updated.');
    }
}
