<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'server_monitoring';
    protected $fillable = ['server_id', 'service_name', 'hostname', 'status'];
    protected $primaryKey = 'id';

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
