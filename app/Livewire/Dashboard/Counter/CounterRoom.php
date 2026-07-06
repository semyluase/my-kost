<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\Category;
use App\Models\Home;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CounterRoom extends Component
{
    #[Computed()]
    function listCategories()
    {
        return Category::where('is_active', true)
            ->get();
    }

    #[Computed()]
    function listBranch()
    {
        if (Auth::user()->role->slug == 'super-admin') {
            return Home::where('is_active', true)
                ->get();
        }

        return Home::where('is_active', true)
            ->where('id', Auth::user()->home_id)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.counter.counter-room');
    }
}
