<?php

namespace App\Livewire\Admin\Account;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class ChangePassword extends Component
{

    #[Layout('layouts.admin')]
    #[Title('Ubah Password | Desa Kepuh')]
    public function render()
    {
        return view('livewire.admin.account.change-password');
    }
}
