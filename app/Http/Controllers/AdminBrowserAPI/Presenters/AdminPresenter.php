<?php

namespace App\Http\Controllers\AdminBrowserAPI\Presenters;

use App\Http\Presenters\BasePresenter;
use App\Models\Eloquents\Admin\Admin;

class AdminPresenter extends BasePresenter
{
    public function __construct(private Admin $admin)
    {
    }

    public function toArray(): array
    {
        return array_merge($this->admin->toArray(), [
            'canDelete' => $this->admin->canDelete(),
        ]);
    }
}
