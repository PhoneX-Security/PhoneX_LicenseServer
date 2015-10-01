<?php namespace Phonex\Http\Requests;

use Phonex\Http\Requests\Request;

class CreateUserRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'username' => 'required|max:255|unique:users',
			'password' => 'required|min:8|max:255',

//            'groups' => '',
//            'roles' => '',

			'starts_at' => 'required|date_format:"d-m-Y"',
			'product_id' => 'required|exists:products,id',
		];
	}

}
