<?php

namespace App\Http\Controllers\MemberWeb;

use App\Http\Controllers\Controller;
use App\Http\HttpStatus;
use App\Models\Eloquents\Event\Order\EventOrderPasswordResetToken;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PasswordResetIndexController extends Controller
{
    public function __invoke($token): Factory | View | Application
    {
        // todo トークンを適宜削除する方法の追加
        $tokenModel = EventOrderPasswordResetToken::query()->get()
            ->filter(static fn (EventOrderPasswordResetToken $tokenModel) => password_verify($token, $tokenModel->token))
            ->first();

        if ($tokenModel === null) {
            abort(HttpStatus::NOT_FOUND);
        }

        return view('member.index', [
            'defaultLang'      => 'ja',
            'tokenEmail'       => $tokenModel->email,
            'tokenOrderNumber' => $tokenModel->order_number,
        ]);
    }
}
