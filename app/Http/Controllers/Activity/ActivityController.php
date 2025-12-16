<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /**
     * Activity list
     */
    public function index()
    {
        $activities = Activity::with('user')
            ->latest()
            ->limit(500)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'user_name' => $a->user?->name ?? 'System',
                'action' => $a->action,
                'entity' => class_basename($a->subject_type),
                'entity_id' => $a->subject_id,
                'before' => $a->before,
                'after' => $a->after,
                'created_at' => $a->created_at->format('Y-m-d H:i:s'),
                'can_restore' => $a->can_restore,
            ]);

        return Inertia::render('activities', [
            'activities' => $activities,
        ]);
    }

    /**
     * Restore activity
     */
    public function restore(Activity $activity): RedirectResponse
    {
        abort_unless($activity->can_restore, 403);

        $modelClass = $activity->subject_type;
        
        if ($activity->action === 'updated' && !is_array($activity->before)) {
            return back()->withErrors('Nothing to restore for this activity.');
        }

        match ($activity->action) {
            'updated' => $this->restoreUpdated($activity, $modelClass),
            'created' => $this->restoreCreated($activity, $modelClass),
            'deleted' => $this->restoreDeleted($activity, $modelClass),
            default => abort(400, 'Unsupported activity type'),
        };

        $activity->update([
            'can_restore' => false,
        ]);

        return back();
    }

    /**
     * Restore UPDATED record
     */
    protected function restoreUpdated(Activity $activity, string $modelClass): void
    {
        $model = $modelClass::findOrFail($activity->subject_id);

        foreach ($activity->before as $field => $value) {

            if (in_array($field, [
                'id',
                'created_at',
                'updated_at',
                'deleted_at',
            ], true)) {
                continue;
            }

            $model->$field = $value;
        }

        $model->timestamps = false;
        $model->save();
        $model->timestamps = true;
    }

    /**
     * Restore CREATED record (delete it)
     */
    protected function restoreCreated(Activity $activity, string $modelClass): void
    {
        $modelClass::whereKey($activity->subject_id)->delete();
    }

    /**
     * Restore DELETED record (recreate it)
     */
    protected function restoreDeleted(Activity $activity, string $modelClass): void
    {
        if (!is_array($activity->before)) {
            abort(400, 'Cannot restore deleted record.');
        }

        if ($modelClass::whereKey($activity->subject_id)->exists()) {
            return;
        }

        $data = $activity->before;
        unset(
            $data['created_at'],
            $data['updated_at'],
            $data['deleted_at']
        );

        $modelClass::create($data);
    }

    /**
     * View changes for UPDATED activity
     */
    public function changes(Activity $activity)
    {
        abort_unless($activity->action === 'updated', 404);

        return Inertia::render('Activities/Changes', [
            'activity' => [
                'id' => $activity->id,
                'user_name' => $activity->user?->name ?? 'System',
                'entity' => class_basename($activity->subject_type),
                'entity_id' => $activity->subject_id,
                'before' => $activity->before,
                'after' => $activity->after,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

}
