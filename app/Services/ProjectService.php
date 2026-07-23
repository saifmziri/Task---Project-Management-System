<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    /**
     * Get all projects (optionally with tasks).
     */
    public function getAll(bool $withTasks = false): Collection
    {
        return Project::query()
            ->when($withTasks, fn ($query) => $query->with([
                'tasks',
                'tasks.user',
            ]))
            ->latest()
            ->get();
    }

    /**
     * Get a single project by ID (optionally with tasks).
     */
    public function getById(int|string $id, bool $withTasks = false): ?Project
    {
        return Project::query()
            ->when($withTasks, fn ($query) => $query->with([
                'tasks',
                'tasks.user',
            ]))
            ->find($id);
    }

    /**
     * Create a new project.
     */
    public function create(array $data, ?int $userId = null): Project
    {
        if ($userId) {
            $data['created_by'] = $userId; 
        }

        return Project::create($data);
    }

    /**
     * Update an existing project.
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project->fresh();
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): bool
    {
        return (bool) $project->delete();
    }
}