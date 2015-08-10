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
            'group_id' => 'required|exists:groups,id',
            'license_type_id' => 'exists:license_types,id',
            'license_func_type_id' => 'exists:license_func_types,id',
			'number' => 'required|integer|between:1,100',
            'email' => 'email',
            'parent_username' => 'exists:users,username',
			'expires_at' => 'sometimes|date_format:"d-m-Y"',
		];
	}

}
