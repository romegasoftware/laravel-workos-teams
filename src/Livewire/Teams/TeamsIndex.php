<?php

namespace RomegaSoftware\WorkOSTeams\Livewire\Teams;

use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

#[Title('Teams')]
class TeamsIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function deleteTeam(TeamContract $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        Flux::toast(
            heading: 'Team Deleted',
            text: 'Team deleted successfully!',
            variant: 'success',
        );
    }

    public function getTeamProperty()
    {
        $user = Auth::user();

        $collection = $user->allTeams()
            ->when($this->search, function ($collection) {
                return $collection->filter(function ($team) {
                    return str_contains(strtolower($team->name), strtolower($this->search)) ||
                        str_contains(strtolower($team->description ?? ''), strtolower($this->search));
                });
            })
            ->when($this->filter, function ($collection) {
                return $collection->filter(function ($team) {
                    return $team->members->contains('id', $this->filter);
                });
            });

        // Manually paginate the collection
        $page = $this->page ?? 1;
        $perPage = 10;
        $items = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
