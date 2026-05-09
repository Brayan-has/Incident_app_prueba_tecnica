<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'created_user_id',
        'assigned_user_id',
        'expiration_date',
    ];

    # relation for the user who created the request
    # to use in a query like $request->createdBy->name or $user->createdRequests->name
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    # relation for the user assigned to the request
    # to use in a query like $user->assignedRequests->name or $request->assignedTo->name 
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
