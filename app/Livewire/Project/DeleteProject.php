<?php

namespace App\Livewire\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class DeleteProject extends Component
{
    public array $parameters;

    public int $project_id;

    public bool $disabled = false;

    public string $projectName = '';

    public function mount()
    {
        $this->parameters = get_route_parameters();
        $this->projectName = Project::findOrFail($this->project_id)->name;
    }

    public function delete($password)
    {
        if (!Hash::check($password, Auth::user()->password)) {
            $this->addError('password', 'The provided password is incorrect.');
            return;
        }
        $this->validate([
            'project_id' => 'required|int',
        ]);
        $project = Project::findOrFail($this->project_id);
        if ($project->applications->count() > 0) {
            return $this->dispatch('error', 'Project has resources defined, please delete them first.');
        }
        $project->delete();

        return redirect()->route('project.index');
    }
}
