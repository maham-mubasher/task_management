<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
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
            'description' => 'required|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'priority_id' => 'nullable|exists:priorities,id',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,png,svg,mp4,csv,txt,doc,docx|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The task title is required.',
            'description.required' => 'The task description is required.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'priority_id.exists' => 'The selected priority is invalid.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
            'attachments.*.mimes' => 'Each attachment must be a valid file type: jpg, png, svg, mp4, csv, txt, doc, docx.',
        ];
    }
}
