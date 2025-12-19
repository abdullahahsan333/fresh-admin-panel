<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'description',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
