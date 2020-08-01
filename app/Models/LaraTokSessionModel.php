<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaraTokSessionModel extends Model
{
    protected $table = 'laratok_sessions';

    protected $fillable = [
      'instructor_id',
      'session_name',
      'sessionId',
      'media_mode',
      'archive_mode',
      'location'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens() {
      return $this->hasMany('LaraTok\LaraTokTokenModel');
    }
}
