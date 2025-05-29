<?php

namespace App\Http\Requests\User;

use App\Exceptions\BankAccountNeededException;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class WithdrawalRequest extends FormRequest
{
    private Wallet $wallet;
    private bool $bankAccountMissing = false;

    public function getWallet(): Wallet {
        return $this->wallet;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'topup_amount' => [
                'required',
                'numeric',
                'min:' . Withdrawal::MIN_AMOUNT,
                'max:' . Withdrawal::MAX_TRANSACTION_AMOUNT,
                'regex:/^\d+(\.\d{1,2})?$/',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    $userWallet = Wallet::retrieveOrCreateCache($user->id);      
                    $this->wallet = $userWallet;              
                    
                    if (!$userWallet || !$userWallet?->has_bank_account) {
                        $this->bankAccountMissing = true;
                        $fail('BANK_ACCOUNT_REQUIRED'); // This will be handled in failedValidation
                        return;
                    }
                    
                    if ($userWallet && $value > $userWallet->usable_balance) {
                        $fail('Insufficient wallet balance for this withdrawal.');
                        return;
                    }
                    
                    if (!Withdrawal::checkDailyLimit($user->id, $value)) {
                        $remainingLimit = Withdrawal::getRemainingDailyLimit($user->id);
                        $fail("Daily withdrawal limit exceeded. You can withdraw up to RM " . number_format($remainingLimit, 2) . " more today.");
                        return;
                    }
                },
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // Check if the failure is due to missing bank account
        if ($this->bankAccountMissing) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'error' => 'BANK_ACCOUNT_REQUIRED',
                    'message' => 'You must link a bank account before making a withdrawal.',
                    'link' => route('wallet.payment-method'),
                    'action_required' => true
                ], 422)
            );
        }

        // For other validation errors, use default Laravel behavior
        parent::failedValidation($validator);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'topup_amount.required' => 'Please enter a withdrawal amount.',
            'topup_amount.numeric' => 'The withdrawal amount must be a valid number.',
            'topup_amount.min' => 'The minimum withdrawal amount is RM ' . number_format(Withdrawal::MIN_AMOUNT, 2) . '.',
            'topup_amount.max' => 'The maximum withdrawal amount per transaction is RM ' . number_format(Withdrawal::MAX_TRANSACTION_AMOUNT, 2) . '.',
            'topup_amount.regex' => 'The withdrawal amount can have at most 2 decimal places.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'topup_amount' => round((float) $this->topup_amount, 2),
        ]);
    }

    /**
     * Get the validated withdrawal amount.
     */
    public function getWithdrawalAmount(): float
    {
        return (float) $this->validated()['topup_amount'];
    }

    /**
     * Get validation attributes for better error messages.
     */
    public function attributes(): array
    {
        return [
            'topup_amount' => 'withdrawal amount',
        ];
    }
}