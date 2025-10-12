<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\Category;
use Livewire\Component;

class CounterRoom extends Component
{
    public function render()
    {
        $categories = Category::where('is_active', true)
            ->get();

        return view('livewire.dashboard.counter.counter-room', [
            'categories'    =>  $categories
        ]);
    }
}
