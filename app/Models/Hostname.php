<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostname extends Model
{
    use HasFactory;

    protected $table = 'hostname';
    protected $fillable = ['project_id', 'server_id', 'hostname'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
