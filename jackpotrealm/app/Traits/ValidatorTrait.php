<?php


namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatorTrait
{

    public function validator($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        $error = false;

        try {

            $validator->validate();

        } catch (ValidationException $e) {

            if ($validator->failed()) {

                $fail = $validator->failed();

                $field = array_key_first($fail);
                $cause = array_key_first($fail[$field]);
                $error = $cause.'.'.$field;

            }

        }

        return $error;
    }

}

?>
