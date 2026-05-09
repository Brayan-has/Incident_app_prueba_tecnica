<?php

namespace App\Concerns\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    
    /*
    
     function with the parameter:
        - $cacheKey: the key to store the data in the cache
        - $ttl: time to live for the cache in seconds
        - $search: the search term
        - $id: the id for filtering the data
        - $query: the query to be executed
        - $filter: the filter to be applied to the query
        - $tag: the tag to be applied to the cache
    */ 
    public function cacheData($cacheKey, $ttl, $id, $query, $filter, $search, $tag)
    {
        $cacheInstance = Cache::supportsTags() ? Cache::tags($tag) : Cache::driver();

        $pagination = $cacheInstance->remember($cacheKey, $ttl, function () use ($search, $id, $query, $filter) 
        {
        
            // filter the data  
            $filteredQuery = $this->filterData($search, $query, $filter, $id);
    
            # paginate the data en return it the paginated
            $endpointDataPagination = $filteredQuery->paginate(10);
            $endpoinPaginated = $this->paginate($endpointDataPagination);
    
            // check if ther's data
            if (empty($endpoinPaginated['data']))
            {
                return response()->json(['message' => 'No data found'], 404);
            }

            return $endpoinPaginated;

        });

        return $pagination;

    }
}
