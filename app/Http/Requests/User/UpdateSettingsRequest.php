<?php

namespace App\Http\Requests\User;

use App\Exceptions\SettingsException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    private $matchingAction;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization needs
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $settingsAction = config('constants.SETTINGS_ROUTE_ACTION');

        return [
            'eventType' => ['required', 'integer', function ($attribute, $value, $fail) use ($settingsAction) {
                $matchingAction = collect($settingsAction)
                    ->first(function ($config) use ($value) {
                        return $config['key'] == $value;
                    });
                
                $this->matchingAction = $matchingAction;

                if (!$matchingAction) {
                    $fail("Invalid settings action key: {$value}");
                }
            }],
            'newEmail' => 'sometimes|email',
            'currentPassword' => 'sometimes',
            'newPassword' => 'sometimes|min:6',
            'newRecoveryEmail' => 'sometimes|email',
        ];
    }

    /**
     * Validate settings configuration
     *
     * @throws SettingsException
     */
   

    /**
     * Get matching action configuration
     */
    public function getMatchingAction(): array
    {
        
        return $this->matchingAction;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'eventType.required' => 'The event type is required.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422)
        );
    }
}
