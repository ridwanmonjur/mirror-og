<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Models\ParticipantCoupon;
use App\Models\UserCoupon;

class RedeemCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    private ParticipantCoupon $coupon ;

    public function getCoupon () : ParticipantCoupon {
        return $this->coupon;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $this->validateCoupon($value, $fail);
                }
            ],
        ];
    }

    protected function validateCoupon($code, $fail)
    {
        $user = $this->get('user');
        $coupon = ParticipantCoupon::where('code', $code)->first();
        if (!$coupon) {
            $fail("No such coupon exists: $code.");
            return;
        }

        $this->coupon = $coupon;


        if (!$coupon->is_active) {
            $fail('This coupon is currently inactive.');
            return;
        }

        if ($coupon->expires_at && $coupon->expires_at < now()) {
            $fail('This coupon has expired.');
            return;
        }

        if (!$coupon->is_public) {
            $userCoupon = UserCoupon::where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();

            if (!$userCoupon) {
                $fail('This coupon is not available for your account.');
                return;
            }

            if ($userCoupon->redeemed_at) {
                $fail('You have already redeemed this coupon.');
                return;
            }
        } else {
            $existingRedemption = UserCoupon::where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->whereNotNull('redeemed_at')
                ->first();

            if ($existingRedemption) {
                $fail('You have already redeemed this coupon.');
                return;
            }
        }
    }

   
    public function messages(): array
    {
        return [
            'coupon_code.required' => 'Coupon code is required.',
            'coupon_code.string' => 'Coupon code must be a valid string.',
            'coupon_code.max' => 'Coupon code cannot exceed 255 characters.',
            'coupon_code.exists' => 'Invalid coupon code.',
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