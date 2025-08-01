<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Models\SystemCoupon;
use App\Models\UserCoupon;

class RedeemCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    private ?SystemCoupon $coupon;

    private ?UserCoupon $userCoupon;

    public function getCoupon(): SystemCoupon
    {
        return $this->coupon;
    }

    public function getUserCoupon(): UserCoupon
    {
        return $this->userCoupon;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $this->validateCoupon($value, $fail);
                },
            ],
        ];
    }

    protected function validateCoupon($code, $fail)
    {
        if (! $code) {
            return;
        }
        $user = $this->get('user');
        $couponQ = SystemCoupon::where('code', $code);

        if ($user->role == 'ORGANIZER') {
            $coupon = $couponQ->where('for_type', 'organizer')->first();
        } else {
            $coupon = $couponQ->where('for_type', 'participant')->first();
        }

        if (! $coupon) {
            $fail("No such coupon exists: $code.");

            return;
        }

        $this->coupon = $coupon;

        if (! $coupon->is_active) {
            $fail('This coupon is currently inactive.');

            return;
        }

        if ($coupon->expires_at && $coupon->expires_at < now()) {
            $fail('This coupon has expired.');

            return;
        }

        $userCoupon = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        $this->userCoupon = $userCoupon;

        if (! $userCoupon) {
            if (! $coupon->is_public) {
                $fail('This coupon is not available for your account.');

                return;
            }

        }

        if ($userCoupon->redeemable_count >= $coupon->redeemable_count) {
            $fail('You have already redeemed this coupon too many times.');

            return;
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
        $error = $validator->errors()->first();

        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => $error,
                ], 422)
            );
        }
        session()->flash('errorMessageCoupon', $error);

        throw new HttpResponseException(
            redirect()->back()->with('errorMessageCoupon', $error)
                ->withInput(['coupon_code' => $this->coupon_code])
        );
    }
}
