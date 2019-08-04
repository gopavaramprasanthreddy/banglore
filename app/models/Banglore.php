<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Banglore extends Model
{
    protected $table = 'banglore_data';
    protected $fillable = [
        'id',
        'outlet_id',
        'latitude',
        'longitude',
        'address',
        'pincode',
        'updated_at'
    ];
}
