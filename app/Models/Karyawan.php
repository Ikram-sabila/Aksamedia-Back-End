<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'image',
        'name',
        'phone',
        'division_id',
        'position'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
