<?php

declare(strict_types=1);

namespace App\View\Components;

use Override;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    #[Override]
    public function render(): View
    {
        return view('layouts.app');
    }
}
