<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IncidentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $action = $this->route()->getActionMethod();
        
        switch ($action) {
            case 'store':
                return [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'status' => 'required|in:pending,in_progress,resolved,closed',
                    'priority' => 'required|in:low,medium,high,critical',
                    'assigned_user_id' => 'nullable|exists:users,id',
                    'created_user_id' => 'required|exists:users,id',
                    'expiration_date' => 'required|date',
                ];
            case 'update':
                return [
                    'title' => 'sometimes|string|max:255',
                    'description' => 'sometimes|string',
                    'status' => 'sometimes|in:pending,in_progress,resolved,closed',
                    'priority' => 'sometimes|in:low,medium,high,critical',
                    'assigned_user_id' => 'nullable|exists:users,id',
                    // 'created_user_id' => 'sometimes|exists:users,id',
                    'expiration_date' => 'sometimes|date',
                ];
            default:
                return [];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
