<?php

require_once 'vendor/autoload.php';

use NovaVision\ActiveRecord\Model;

/**
 * @property int $id
 */
class User extends Model
{
    protected array $fillable = [
        'email'
    ];

    protected array $hidden = [
        'password'
    ];
}

/**
 * @property string $title
 * @property string $description
 * @property int $user_id
 */
class Post extends Model
{
    protected array $fillable = [
        'title', 'description', 'user_id'
    ];
}

$post = Post::query()->where('id', '=', 4)->first();
$post->delete();