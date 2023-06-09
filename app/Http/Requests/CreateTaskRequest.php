<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class CreateTaskRequest extends FormRequest
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
     * @return array
     */
    #[ArrayShape(["project_id" => "string", "name" => "string"])]
    public function rules(): array
    {
        return [
            "project_id" => "required|numeric|exists:projects,id",
            "name" => "required"
        ];
    }
}
