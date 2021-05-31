<?php

namespace App\Models\Eloquents\Member\Concerns;

use App\ConstantValues\Person;
use App\Library\Rules\Password\PasswordCheck;
use App\Library\Rules\UniqueInNotSoftDeleted;
use App\Models\Eloquents\Member;
use Illuminate\Validation\Rules\In;

/**
 * バリデーションルールまとめ。何かルールを変える時は他のルールの同じ対象も変えること
 */
trait MemberValidationRules
{
    public static function memberUpdateRules($currentTgtId): array
    {
        return [
            'email' => [
                'nullable',
                'string',
                'max:255',
                'email:rfc',
                new UniqueInNotSoftDeleted(new Member(), 'email', $currentTgtId)
            ],
        ];
    }

    public static function memberUpdatePasswordRules(): array
    {
        return [
            'oldPassword'        => ['required', new PasswordCheck(Member::getAuthProvider(), auth('api')->user())],
            'newPassword'        => ['required', 'string', 'min:8', 'max:255',],
            'newPasswordConfirm' => ['required', 'string', 'min:8', 'max:255', 'same:newPassword'],
        ];
    }

    public static function memberUpdatePasswordAttributes(): array
    {
        return [
            'oldPassword'        => '変更前のパスワード',
            'newPassword'        => '新しいパスワード',
            'newPasswordConfirm' => '新しいパスワード（再入力）',
        ];
    }

    public static function memberResetPasswordRules(): array
    {
        return [
            'token'           => 'required',
            'email'           => ['required', 'string', 'max:255', 'email:rfc'],
            'password'        => ['required', 'string', 'min:8', 'max:255',],
            'passwordConfirm' => ['required', 'string', 'min:8', 'max:255', 'same:password'],
        ];
    }

    public static function memberResetPasswordAttributes(): array
    {
        return [
            'email'           => 'メールアドレス',
            'password'        => 'パスワード',
            'passwordConfirm' => 'パスワード（確認）',
        ];
    }

    public static function adminCreateRules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'gender'          => ['required', 'integer', new In(array_keys(Person::GENDERS))],
            'birthday'        => ['nullable', 'date'],
            'email'           => [
                'nullable',
                'string',
                'max:255',
                'email:rfc',
                new UniqueInNotSoftDeleted(new Member(), 'email')
            ],
            'password'        => ['required', 'string', 'min:8', 'max:255',],
            'passwordConfirm' => ['required', 'string', 'min:8', 'max:255', 'same:password'],
            'status'          => ['required', new In(array_keys(self::STATUS_LIST))]
        ];
    }

    public static function adminUpdateRules($currentTgtId): array
    {
        return [
            'name'            => ['string', 'max:255'],
            'birthday'        => ['nullable', 'date'],
            'email'           => [
                'nullable',
                'string',
                'max:255',
                'email:rfc',
                new UniqueInNotSoftDeleted(new Member(), 'email', $currentTgtId)
            ],
            'password'        => ['nullable', 'string', 'min:8', 'max:255',],
            'passwordConfirm' => ['nullable', 'string', 'min:8', 'max:255', 'same:password'],
            'status'          => ['nullable', new In(array_keys(self::STATUS_LIST))]
        ];
    }

    public static function ruleAttributes(): array
    {
        return [
            'name'             => '名前',
            'gender'           => '性別',
            'birthday'         => '生年月日',
            'email'            => 'メールアドレス',
            'password'         => 'パスワード',
            'password_confirm' => 'パスワード（確認）',
            'status'           => 'ステータス',
        ];
    }
}
