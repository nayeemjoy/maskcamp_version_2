<?php
namespace App\Http\Transformers;

use App\Post;
use League\Fractal;

class PostTransformer extends Fractal\TransformerAbstract
{
	public function transform(Post $post)
	{
	    return [
	        'id'      => (int) $post->id,
	        'post'   => $post->post
	    ];
	}
}