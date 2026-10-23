<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewerApplicationStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // 折り返しの連絡に使うので、ドメインが実在するアドレスだけを受け付ける
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'suno_id' => ['required', 'string', 'max:255'],
            'motivation' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.email' => '受信できるメールアドレスを入力してください。',
        ];
    }
}
