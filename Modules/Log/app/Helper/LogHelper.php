<?php

namespace Modules\Log\Helper;

use Illuminate\Database\Eloquent\Model;

class LogHelper
{
    public static function getCreator(Model $model, array $fields): string
    {
        $data = '';

        foreach ($fields as $field) {
            if ($model->$field) {
                $data .= $model->$field.' ';
            }
        }

        return $data;
    }
}
