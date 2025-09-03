<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:company_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['true', 'false', '0', '1', true, false, 0, 1])],
        ];
    }

    protected function prepareForValidation()
    {
        // Convert string boolean to actual boolean
        if ($this->has('status')) {
            $status = $this->status;

            if ($status === 'true' || $status === '1') {
                $this->merge(['status' => true]);
            } elseif ($status === 'false' || $status === '0') {
                $this->merge(['status' => false]);
            }
        }
    }
}
