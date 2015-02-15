<?php namespace Phonex\Http\Requests;

use Phonex\Http\Requests\Request;

class UpdateUserRequest extends Request {

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
			// giving access
			'password' => 'confirmed|min:8|max:255|required_with:has_access',
		];
	}

}
