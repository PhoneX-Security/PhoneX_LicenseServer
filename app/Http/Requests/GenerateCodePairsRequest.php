<?php namespace Phonex\Http\Requests;

class GenerateCodePairsRequest extends Request {

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
            'group_id' => 'exists:groups,id',
            'license_type_id' => 'exists:license_types,id',
			'number_of_pairs' => 'required|integer|between:1,100',
            'email' => 'required|email',
            'parent_username' => 'exists:users,username',
			'expires_at' => 'sometimes|date_format:"d-m-Y"',
		];
	}

}
