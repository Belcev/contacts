<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
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
        $contact = $this->route('contact');
        if ($contact instanceof Contact) {
            $contact->getKey();
        }

        return [
            'email'      => ['required', 'email:rfc,filter', 'max:255',
                Rule::unique('contacts', 'email')->ignoreModel($contact) ],
            'first_name' => ['required','string','max:255'],
            'last_name'  => ['required','string','max:255'],
        ];
    }
}
