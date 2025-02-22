<?php

namespace App\View\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IconOpen extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $href = '#',
        public int $version = 1
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table.icon-open');
    }
}
