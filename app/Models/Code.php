<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;
    protected $table = "codes";
    protected $fillable = ['user_id','code', 'active'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
