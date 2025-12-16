<?php

namespace App\Observers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    public function created(Model $model): void
    {
        Activity::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'subject_type' => get_class($model),
            'subject_id'   => $model->getKey(),
            'before'       => null,
            'after'        => $model->getAttributes(),
            'can_restore'  => true,
        ]);
    }

    public function updating(Model $model): void
    {
        $before = $model->getOriginal();
        $after  = $model->getDirty();
        if (empty($after)) {
            return;
        }

        Activity::create([
            'user_id'      => auth()->id(),
            'action'       => 'updated',
            'subject_type' => get_class($model),
            'subject_id'   => $model->getKey(),
            'before'       => array_intersect_key($before, $after),
            'after'        => $after,
            'can_restore'  => true,
        ]);
    }

    public function deleted(Model $model): void
    {
        Activity::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'subject_type' => get_class($model),
            'subject_id'   => $model->getKey(),
            'before'       => $model->getAttributes(),
            'after'        => null,
            'can_restore'  => true,
        ]);
    }
}
