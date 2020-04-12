<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];

    /*
     * The roles that belong to this user
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @param Tag $tag
     * @return mixed
     */
    public function hasTag(Tag $tag) {
        return $this->tags->contains($tag);
    }

    public function justTags() {
        return $this->tags()->pluck('name')->toArray();
    }
}
