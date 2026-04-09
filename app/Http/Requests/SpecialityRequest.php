<?php

namespace App\Http\Requests;

use App\Models\Speciality;
use Illuminate\Foundation\Http\FormRequest;

class SpecialityRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        $data = $this->route('speciality');
        $id = $data?->id ?? null;

        if ($routeName === 'speciality-status-update') {
            return Speciality::specialityStausUpdateRules();
        }

        return Speciality::rules($id);
    }
}
