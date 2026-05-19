<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'tbl_messages';
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'body',
        'parent_id',
        'status',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replies()
    {
        return $this->hasMany(Messages::class, 'parent_id')->orderBy('created_at');
    }
}
