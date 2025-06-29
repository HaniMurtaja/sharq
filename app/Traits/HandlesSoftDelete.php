<?php
namespace App\Traits;

trait HandlesSoftDelete
{
    public function softDeleteWithMeta($model)
    {
        if (!$model) {
            throw new \Exception('Model not found');
        }
       

        $model->update([
            'deleted_by' => auth()->id(),
            'email' => $model->email . '.deleted.' . uniqid(),
        ]);

        $model->delete();
    }
}
