<?php

namespace App\Http\Controllers\AdminBrowserAPI;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use RuntimeException;

class BaseAdminBrowserAPIController extends Controller
{
    use ApiResponseTrait;

    protected string $callControllerDateTime;

    public function __construct()
    {
        $this->callControllerDateTime = now()->format('Y-m-d H:i:s');
    }

    /**
     * Resolve request signature.
     *
     * @throws RuntimeException
     * @return string
     */
    protected function resolveRequestSignature(): string
    {
        $request = request();
        if ($user = $request->user()) {
            return sha1($request->url().$user->getAuthIdentifier());
        }

        if ($route = $request->route()) {
            return sha1($route->uri().$route->getDomain().'|'.$request->ip());
        }

        throw new \RuntimeException('要求署名を生成できません。ルートが利用できません。');
    }
}
