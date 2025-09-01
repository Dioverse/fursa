<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LanguageUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // For updates, allow partial sections/languages, but keep same structure
            'content' => ['required', 'array', 'min:1'],
            'content.*' => ['required', 'array', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Content is required and must be a JSON object.',
            'content.array' => 'Content must be a JSON object (key/value).',
            'content.min' => 'Provide at least one section to update.',
            'content.*.array' => 'Each section must be an object of language translations.',
            'content.*.min' => 'Each section must include at least one language key.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $content = $this->input('content', []);

            if (!$this->isAssoc($content)) {
                $v->errors()->add('content', 'Content must be an object with section names as keys.');
                return;
            }

            $sectionNameRegex = '/^[a-z][a-z0-9_\-]*$/i';
            $localeRegex = '/^[a-z]{2,3}(?:-[A-Z]{2})?$/';

            if (count($content) > 200) {
                $v->errors()->add('content', 'Too many sections (max 200).');
            }

            foreach ($content as $section => $translations) {
                if (!is_string($section) || !preg_match($sectionNameRegex, $section)) {
                    $v->errors()->add("content.$section", "Invalid section name: $section.");
                    continue;
                }

                if (!is_array($translations) || !$this->isAssoc($translations)) {
                    $v->errors()->add("content.$section", 'Section must be an object of language=>value pairs.');
                    continue;
                }

                if (count($translations) > 20) {
                    $v->errors()->add("content.$section", 'Too many languages in a section (max 20).');
                }

                foreach ($translations as $lang => $value) {
                    if (!is_string($lang) || !preg_match($localeRegex, $lang)) {
                        $v->errors()->add("content.$section.$lang", "Invalid language code: $lang. Use en, fr, or en-US style.");
                    }
                    if (!is_string($value)) {
                        $v->errors()->add("content.$section.$lang", 'Translation value must be a string.');
                    } else {
                        $trimmed = trim($value);
                        if ($trimmed === '') {
                            $v->errors()->add("content.$section.$lang", 'Translation value cannot be empty.');
                        }
                        if (mb_strlen($trimmed) > 5000) {
                            $v->errors()->add("content.$section.$lang", 'Translation is too long (max 5000 chars).');
                        }
                    }
                }
            }
        });
    }

    private function isAssoc(array $arr): bool
    {
        return !array_is_list($arr); // PHP 8.1+:
        // return array_keys($arr) !== range(0, count($arr) - 1); // PHP <8.1
    }
}
