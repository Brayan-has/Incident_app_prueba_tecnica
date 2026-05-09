<?php

namespace App\Concerns\Traits;

trait filterTrait
{
    
public function filterData($search, $query, $fields, $id)
    {
        if ($search) {

            $query->where(function ($q) use ($search, $fields) {

                foreach ($fields as $field) {

                    $q->orWhere($field, 'LIKE', "%$search%");

                    if ($field === 'id' && is_numeric($search)) {
                        $q->orWhere($field, $search);
                    }
                }

               
            });
        }
        
        if ($id) {
            $query->where('id', $id);
        }
        
        return $query;
    }
}
