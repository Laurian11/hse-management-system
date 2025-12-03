<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Config;

class DesignSystem extends Component
{
    public array $colors;
    public array $typography;
    public array $spacing;
    public array $borderRadius;
    public array $shadows;
    public array $transitions;
    public array $zIndex;
    public array $breakpoints;
    public array $components;

    public function __construct()
    {
        $this->colors = Config::get('ui_design.colors');
        $this->typography = Config::get('ui_design.typography');
        $this->spacing = Config::get('ui_design.spacing');
        $this->borderRadius = Config::get('ui_design.border_radius');
        $this->shadows = Config::get('ui_design.shadows');
        $this->transitions = Config::get('ui_design.transitions');
        $this->zIndex = Config::get('ui_design.z_index');
        $this->breakpoints = Config::get('ui_design.breakpoints');
        $this->components = Config::get('ui_design.components');
    }

    public function render()
    {
        return view('components.design-system');
    }
}
