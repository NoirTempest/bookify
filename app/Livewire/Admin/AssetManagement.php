<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class AssetManagement extends Component
{
    public function render()
    {
        return view('livewire.admin.asset-management')
            ->layout('layouts.admin'); // ✅ Fix this line
        ;
    }
}
