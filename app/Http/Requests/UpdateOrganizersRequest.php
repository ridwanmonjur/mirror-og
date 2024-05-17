<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'address.id' => 'nullable',
            'address.addressLine1' => 'nullable|string',
            'address.addressLine2' => 'nullable|string',
            'address.city' => 'nullable|string',
            'address.country' => 'nullable|string',
            'address.user_id' => 'required',
            'userProfile.name' => 'required|string',
            'userProfile.id' => 'required',
            'userProfile.mobile_no' => 'nullable|string',
            'organizer.id' => 'nullable',
            'organizer.industry' => 'nullable|string',
            'organizer.type' => 'nullable|string',
            'organizer.companyName' => 'nullable|string|max:255',
            'organizer.companyDescription' => 'nullable|string|max:1000',
            'organizer.website_link' => 'nullable|url',
            'organizer.instagram_link' => 'nullable|url',
            'organizer.facebook_link' => 'nullable|url',
            'organizer.twitter_link' => 'nullable|url',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
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
            'organizer.website_link.url' => 'Website Link must be a valid URL.',
            'organizer.instagram_link.url' => 'Instagram Link must be a valid URL.',
            'organizer.facebook_link.url' => 'Facebook Link must be a valid URL.',
            'organizer.twitter_link.url' => 'Twitter Link must be a valid URL.',
        ];
    }

    protected function prepareForValidation()
    {
        $attributes = $this->request->all();
        $links = ['website_link', 'instagram_link', 'twitter_link', 'facebook_link'];

        foreach ($links as $link) {
            if (isset($attributes[$link])) {
                if (substr($attributes[$link], -1) === '/') {
                    $attributes[$link] = rtrim($attributes[$link], '/');
                }
            }
        }

        $this->request->replace($attributes);
    }
}
