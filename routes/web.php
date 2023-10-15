<?php

use Illuminate\Support\Facades\Route;

Route::get('/posts', function () {
    $users = \App\Models\User::with('posts.comments')->get();
    $comments = collect();

    \Illuminate\Support\Benchmark::dd([
       'Scenario Each' => fn () => $users->each(function (\App\Models\User $user) use ($comments) {
           $user->posts->each(function (\App\Models\Post $post) use ($comments) {
               $post->comments->each(function (\App\Models\Comment $comment) use ($comments) {
                   $comments->push($comment);
               });
           });
       }),
        'Scenario FlatMap' => fn () => $users->flatMap(function (\App\Models\User $user) {
            return $user->posts->flatMap(function (\App\Models\Post $post) {
                return $post->comments;
            });
        }),
        'Scenario FlatMap (higher order message)' => fn () => $users->flatMap->posts->flatMap->comments,
        'Scenario pluck' => fn () => $users->pluck('posts.*.comments')->flatten(),
    ]);
});
