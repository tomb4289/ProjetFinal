<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavItem extends Component
{
    public string $label;
    public string $icon;
    public string $url;
    /**
     * Create a new component instance.
     */
    public function __construct(string $label, string $icon, string $url)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav-item');
    }
}
