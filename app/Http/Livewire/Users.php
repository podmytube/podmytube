<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public int $nbItemsPerPage = 30;

    public function render()
    {
        $this->authorize('superadmin');

        return view('livewire.users', [
            'users' => $this->usersToDisplay(),
        ]);
    }

    public function usersToDisplay(): LengthAwarePaginator
    {
        $results = User::query()
            ->whereHas('channels', function (Builder $query): void {
                $query->where('active', '=', 1);
            })
            ->orderBy('created_at')
            ->paginate($this->nbItemsPerPage)
        ;
        if ($results->count()) {
            return $results;
        }

        return new LengthAwarePaginator([], 0, $this->nbItemsPerPage);
    }
}
