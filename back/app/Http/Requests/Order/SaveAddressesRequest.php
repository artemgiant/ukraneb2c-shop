<?php


namespace App\Http\Requests\Order;


use Illuminate\Foundation\Http\FormRequest;

class SaveAddressesRequest extends FormRequest
{
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

        $rules['phone'] = array('required',
            function ($attribute, $value, $fail) {
                $phone = phone_numeral_format($this->phone);
                $ifValid = preg_match('%^[0-9]{12}$%', $phone);

                if ($ifValid == false) {
                    $fail("Invalid phone format!");
                }
            });

        $rules['email'] = array('required','email');

//       $val =   \Validator::make($this->all(),$rules);
//        dd($val->errors(),$rules,$this->all(),$this->getMethod());
        return $rules;

    }
}
