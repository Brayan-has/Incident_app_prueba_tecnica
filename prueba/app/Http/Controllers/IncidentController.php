<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;    
use App\Concerns\Traits\PaginationTrait;
use App\Http\Requests\IncidentRequest;
use App\Jobs\ChangeIncidentStatusToExpiredJob;
use Carbon\Carbon;

class IncidentController extends Controller
{
    use PaginationTrait;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incidents = Incident::with(['createdBy:id,name,email', 'assignedTo:id,name,email'])->paginate(15);

        // if theres no data return 404
        if ($incidents->isEmpty()) {
            return response()->json([
                'message' => 'No incidents found'
            ], 404);
        }
        
        $incidents = $this->paginate($incidents);
        
        return response()->json($incidents, 200);
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

        return response()->json([
            'message' => 'Incident created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(IncidentRequest $request)
    {
        $incident = Incident::with(['createdBy:id,name,email', 'assignedTo:id,name,email'])->find($request->id);

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
