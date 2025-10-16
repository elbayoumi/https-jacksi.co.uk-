<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Seller extends Authenticatable
{
use HasFactory, Notifiable, HasApiTokens;


protected $table = 'sellers';


protected $fillable = ['name','email','password','is_active'];
protected $hidden = ['password','remember_token'];


protected function casts(): array
{
return [
'email_verified_at' => 'datetime',
'password' => 'hashed',
'is_active' => 'boolean',
];
}


// Relations
public function clients() { return $this->hasMany(Client::class, 'seller_id'); }
public function invoices() { return $this->hasMany(Invoice::class, 'seller_id'); }
}
