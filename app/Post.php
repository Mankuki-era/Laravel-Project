<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'title', 'content', 'img_url'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function tags() {
        return $this->belongsToMany('App\Tag')->withTimeStamps();
    }

    public function likes() {
        return $this->hasMany('App\Like', 'post_id', 'id');
    }

    public function comments() {
        return $this->hasMany('App\Comment', 'post_id', 'id');
    }
}
