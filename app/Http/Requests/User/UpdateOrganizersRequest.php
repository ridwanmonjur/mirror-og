<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrganizersRequest extends FormRequest
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
            'address' => 'nullable|array',
            'address.id' => 'nullable|exists:user_address,id',
            'address.addressLine1' => 'nullable|required_with:address.city,address.country|string',
            'address.addressLine2' => 'nullable|string|required_with:address.addressLine1,address.country|string',
            'address.city' => 'nullable|required_with:address.addressLine1,address.country|string',
            'address.country' => 'nullable|required_with:address.addressLine1,address.city|string',
            'address.user_id' => 'nullable',
            'userProfile.name' => 'required|string|max:100',
            'userProfile.demo_email' => 'nullable|email',
            'userProfile.id' => 'required',
            'userProfile.mobile_no' => 'nullable|string',
            'organizer.id' => 'required',
            'organizer.industry' => 'nullable|string',
            'organizer.type' => 'nullable|string',
            'organizer.companyName' => 'nullable|string|max:255',
            'organizer.companyDescription' => 'nullable|string|max:255',
            'organizer.website_link' => 'nullable|string',
            'organizer.instagram_link' => 'nullable|string',
            'organizer.facebook_link' => 'nullable|string',
            'organizer.twitter_link' => 'nullable|string',
            'file' => 'sometimes|array|nullable',
            'file.filename' => 'string',
            'file.type' => 'string',
            'file.size' => 'numeric|max:3',
            'file.content' => 'string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'userProfile.name.required' => 'The name field is required.',
            'userProfile.demo_email.email' => 'The email field must be a valid email.',

            'userProfile.id.required' => 'The user profile ID is required.',
            'organizer.id.required' => 'The organizer ID is required.',
            'address.addressLine1.string' => 'Address Line 1 must be a string.',
            'address.addressLine2.string' => 'Address Line 2 must be a string.',
            'address.city.string' => 'City must be a string.',
            'address.country.string' => 'Country must be a string.',
            'userProfile.name.string' => 'Name must be a string.',
            'userProfile.mobile_no.string' => 'Mobile number must be a string.',
            'organizer.industry.string' => 'Industry must be a string.',
            'organizer.type.string' => 'Type must be a string.',
            'organizer.companyName.string' => 'Company Name must be a string.',
            'organizer.companyName.max' => 'Company Name must not exceed 255 characters.',
            'organizer.companyDescription.string' => 'Company Description must be a string.',
            'organizer.companyDescription.max' => 'Company Description must not exceed 1000 characters.',
            'organizer.website_link.string' => 'Website Link must be a valid URL.',
            'organizer.instagram_link.string' => 'Instagram Link must be a valid URL.',
            'organizer.facebook_link.string' => 'Facebook Link must be a valid URL.',
            'organizer.twitter_link.string' => 'Twitter Link must be a valid URL.',
            'file.size' => 'The file is too large, over 3 MB in size',
            

        ];
    }

    private function isEmptyOrNotSet($array, $key): bool
    {
        return !isset($array[$key]) || empty($array[$key]);
    }

    protected function prepareForValidation()
    {
        if ($this->isEmptyOrNotSet($this->address, 'city') &&
        $this->isEmptyOrNotSet($this->address, 'country') &&
        $this->isEmptyOrNotSet($this->address, 'addressLine1')) {
            $this->request->remove('address');
        }

        if ($this->input('organizer')) {
            $organizerData = [...$this->organizer];
            $links = ['website_link', 'instagram_link', 'facebook_link', 'twitter_link'];

            $socialDomains = [
                'instagram_link' => 'instagram.com',
                'facebook_link' => 'facebook.com',
                'twitter_link' => ['twitter.com', 'x.com']
            ];

            foreach ($links as $link) {
                $url = rtrim(trim($organizerData[$link]), '/');
                if (empty($url)) {
                    $organizerData[$link] = null;
                    continue;
                }

                $isValidStructure = fn($url) => preg_match('/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}/', parse_url($url, PHP_URL_HOST) ?? '');
                $normalizeUrl = fn($url) => rtrim($url, '/');
                $getDomain = fn($url) => strtolower(parse_url($url, PHP_URL_HOST) ?? '');

                if (!filter_var($url, FILTER_VALIDATE_URL) || !$isValidStructure($url)) {
                    $modifiedUrl = 'https://' . preg_replace('#^(https?://)?(www\.)?#', '', $url);

                    if (filter_var($modifiedUrl, FILTER_VALIDATE_URL) && $isValidStructure($modifiedUrl)) {
                        $url = $modifiedUrl;
                    } else {
                        throw ValidationException::withMessages([
                            "organizer.{$link}.string" => ["The url '{$url}' is invalid. Please enter a valid URL."]
                        ]);
                    }
                }

                // Check for specific social media domains
                if (isset($socialDomains[$link])) {
                    $domain = preg_replace('/^www\./', '', $getDomain($url));
                    $expectedDomains = (array)$socialDomains[$link];

                    if (!in_array($domain, $expectedDomains)) {
                        throw ValidationException::withMessages([
                            "organizer.{$link}.string" => ["Please provide a valid domain '$socialDomains[$link]' for your link, '$url'."]
                        ]);
                    }
                }

                $organizerData[$link] = $normalizeUrl($url);
            }

            // dd($this->organizer, $organizerData);
            $this->merge(['organizer' => $organizerData]);

        }

    }

    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();
    
        throw new ValidationException($validator, response()->json( [
            'message' => $error,
            'success'=> false
        ], 422));
    }
}
