<?php

namespace App\Http\Requests;

use App\Models\Experience;
use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
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

        $data = $this->route('experience');
        $id = $data?->id ?? null;

        if ($routeName === 'experience-status-update') {
            return Experience::experienceStausUpdateRules();
        }

        return Experience::rules($id);
    }
}
