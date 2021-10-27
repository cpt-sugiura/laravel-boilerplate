<?php

namespace App\Http\Controllers\MemberWeb;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    public function __invoke(): Factory | View | Application
    {
        return view('member.index', [
            'defaultLang' => 'ja',
        ]);
    }
}
