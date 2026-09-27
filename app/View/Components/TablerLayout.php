<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TablerLayout extends Component
{
    public $title;

    public function __construct($title = null)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represents the component.
     * NOTE: Tabler was replaced by CoreUI. This alias now renders the
     * CoreUI shell so all existing <x-tabler-layout> pages keep working.
     */
    public function render(): View
    {
        return view('layouts.coreui');
    }
}
