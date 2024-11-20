<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scedule extends Model
{
  use HasFactory;

  protected $casts = [
    'is_wfa' => 'boolean',
    'is_banned' => 'boolean',
  ];

  protected $fillable = [
    'user_id',
    'shift_id',
    'office_id',
    'is_wfa',
    'is_banned',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
  public function shift(): BelongsTo
  {
    return $this->belongsTo(Shift::class);
  }
  public function office(): BelongsTo
  {
    return $this->belongsTo(Office::class);
  }
}
