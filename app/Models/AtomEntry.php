<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtomEntry extends Model
{
    use HasFactory;

    protected $table = 'atom_entries';

    public function channel() {
        return $this->belongsTo(Channel::class);
    }
}
