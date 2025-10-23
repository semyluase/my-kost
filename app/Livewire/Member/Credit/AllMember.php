<?php

namespace App\Livewire\Member\Credit;

use App\Models\User;
use Livewire\Component;

class AllMember extends Component
{
    public $users = [];
    public $search;

    public function render()
    {
        $this->users = User::where('role_id', 3)
            ->SearchUser(['search' => $this->search])
            ->get();

        return view('livewire.member.credit.all-member', [
            'users' =>  $this->users
        ]);
    }
}
