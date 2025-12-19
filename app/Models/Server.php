<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'servers';
    protected $fillable = ['project_id', 'ip', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
