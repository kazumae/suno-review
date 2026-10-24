<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequestStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 楽曲は SUNO の URL(共有リンク含む)、YouTube 欄は YouTube の URL だけを受け付ける
            'suno_url' => ['required', 'url:http,https', 'max:255', 'regex:#^https?://([a-z0-9-]+\.)*suno\.(com|ai)/#i'],
            'artist_name' => ['required', 'string', 'max:255'],
            'artist_url' => ['required', 'url:http,https', 'max:255'],
            'youtube_url' => ['nullable', 'url:http,https', 'max:255', 'regex:#^https?://([a-z0-9-]+\.)*(youtube\.com|youtu\.be)/#i'],
            'title' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'suno_url.regex' => 'SUNOの楽曲URL（https://suno.com/...）を入力してください。',
            'youtube_url.regex' => 'YouTubeのURL（https://www.youtube.com/... または https://youtu.be/...）を入力してください。',
        ];
    }
}
