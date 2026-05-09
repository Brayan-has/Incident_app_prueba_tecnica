<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;    
use App\Concerns\Traits\PaginationTrait;
use App\Http\Requests\IncidentRequest;
use App\Jobs\ChangeIncidentStatusToExpiredJob;
use Carbon\Carbon;
use App\Concerns\Traits\CacheTrait;
use App\Concerns\Traits\filterTrait;


class IncidentController extends Controller
{
    use PaginationTrait,CacheTrait, filterTrait;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Incident::query()->with(['createdBy:id,name,email', 'assignedTo:id,name,email']);
        // search variable to filter many data with the only parameter search in the url
        $search = request("search");
        $id = request("id");

        // currect page for caching
        $page = request("page", 1);

        //cache key for caching the results of the query, it includes the page number, search term and id for filtering
        $cacheKey = "project_page_{$page}_search_" . md5($search ?? 'none').  "_id_". ($id ?? "none");
        
        // caching fo 1 minute
        $ttl = 60; 


        $filter = ['id','title','description','status','created_by','assigned_to', 'expiration_date'];    

        return $this->cacheData($cacheKey, $ttl, $id, $query, $filter, $search, 'incidents');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IncidentRequest $request)
    {
        $incident = Incident::create($request->validated());
        
        // Dispatch job to change status to expired when the expiration date is passed
        $expirationDate = Carbon::parse($incident->expiration_date)->endOfDay();
        ChangeIncidentStatusToExpiredJob::dispatch($incident)->delay($expirationDate);

        // flush the cache
        Cache::tags('incidents')->flush();
        
        return response()->json([
            'message' => 'Incident created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $incident = Incident::with(['createdBy:id,name,email', 'assignedTo:id,name,email'])->find($id);

        // if theres no data return 404
        if (!$incident) {
            return response()->json([
                'message' => 'Incident not found'
            ], 404);
        }
        
        return response()->json(
            [
                "incident" => $incident,
                "message" => "Incident found successfully"
            ], 
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncidentRequest $request, $id)
    {
        $incident = Incident::with(['createdBy:id,name,email', 'assignedTo:id,name,email'])->find($id);

        // if theres no data return 404
        if (!$incident) {
            return response()->json([
                'message' => 'Incident not found'
            ], 404);
        }
        
        $incident->update($request->validated());

        // flush the cache
        Cache::tags('incidents')->flush();
        
        $incident->save();
        
        return response()->json(
            [
                "message" => "Incident updated successfully"
            ], 
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $incident = Incident::find($id);

        // if theres no data return 404
        if (!$incident) {
            return response()->json([
                'message' => 'Incident not found'
            ], 404);
        }

        $incident->delete();

        // flush the cache
        Cache::tags('incidents')->flush();
        
        return response()->json(
            [
                "message" => "Incident deleted successfully"
            ], 
            200
        );
    }

    // incidents by status
    public function incidentByStatus($status)
    {
         
        $incidents = Incident::with(['createdBy:id,name,email', 'assignedTo:id,name,email'])->where('status', $status)->paginate(15);

        // if theres no data return 404
        if ($incidents->isEmpty()) {
            return response()->json([
                'message' => 'No incidents found'
            ], 404);
        }
        
        $incidents = $this->paginate($incidents);
        
        return response()->json($incidents, 200);
    }

    // Incidents expired
    public function incidentsExpired()
    {
        $incidents = Incident::with(['createdBy:id,name,email', 'assignedTo:id,name,email'])->where('expiration_date', '<', now())->paginate(15);
        
        // if theres no data return 404
        if ($incidents->isEmpty()) {
            return response()->json([
                'message' => 'No incidents found'
            ], 404);
        }
        
        $incidents = $this->paginate($incidents);
        
        return response()->json($incidents, 200);
    }
}
