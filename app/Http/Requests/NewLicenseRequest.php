<?php namespace Phonex\Http\Requests;

use Phonex\Http\Requests\Request;

class NewLicenseRequest extends Request {

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
			'starts_at' => 'required|date_format:"d-m-Y"',
			'product_id' => 'required|exists:products,id',
		];
	}

}
