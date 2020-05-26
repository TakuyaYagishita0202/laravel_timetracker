<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'name', 'memo', 'category_id', 'category_name', 'category_color', 'user_id', 'stopped_at', 'started_at'
    ];

    /**
     * {@inheritDoc}
     */
    protected $dates = ['started_at', 'stopped_at'];    

    /**
     * {@inheritDoc}
     */
    protected $with = ['user'];

    protected $hidden = ['user'];

    /**
     * Get the related user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get timer for current user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMine($query)
    {
        return $query->whereUserId(auth()->user()->id);
    }

    /**
     * Get the running timers
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRunning($query)
    {
        return $query->whereNull('stopped_at');
    }
}
