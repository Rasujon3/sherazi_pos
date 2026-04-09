<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StaffRequest extends FormRequest
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

        $data = $this->route('staff');
        $id = $data?->id ?? null;

        if ($routeName === 'staff-status-update') {
            return Staff::staffStausUpdateRules();
        }

        if ($routeName === 'staffs.update') {
            return Staff::updateRules($id);
        }

        if ($routeName === 'staffs.store.services') {
            return Staff::storeServiceRules();
        }

        if ($routeName === 'staffs.update.services') {
            return Staff::storeServiceRules();
        }

        if ($routeName === 'api.sp.login') {
            return Staff::spLoginRules();
        }

        if ($routeName === 'sp.addServices.store' || $routeName === 'sp.addServices.update') {
            return Staff::apiStoreServiceRules();
        }

        return Staff::rules($id);
    }
}
