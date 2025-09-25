<?php

namespace App\Livewire\Dashboard\Table;

use App\Models\Category;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class TableRoom extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $length = 10;
    public $category = null;
    public $roomNumber;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categoryList = Category::where('is_active', true)->get();

        $rooms = Room::with(['home', 'category', 'rent', 'rent.member', 'rent.member.user'])
            ->where('home_id', Auth::user()->home_id)
            ->searchCategory($this->category)
            ->search(['search'  =>  $this->roomNumber])
            ->paginate($this->length);

        return view('livewire.dashboard.table.table-room', [
            'rooms' =>  $rooms,
            'categories'    =>  $categoryList,
        ]);
    }
}
