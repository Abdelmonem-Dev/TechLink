<?php

include_once __DIR__ . '/../Models/Post.php';

class PostController
{
    public static function AddComment($post_id, $user_id, $user_name, $comment_text, $parent_comment_id = null):bool
    {
        return CommentModel::AddComment($post_id, $user_id, $user_name, $comment_text, $parent_comment_id);
    }
    public static function getComments(int $postId): array
    {
        // Simulated static array of comments
        $comments = [ ];
        return $comments;
    }
    
    public static function getByPostID(int $details): array
    {
        return Post::getByPostID($details);
    }
    public static function create(Post $details): ?Post
    {
        return Post::create($details);
    }
    public static function getPosts($query,$searchTerm = ''): ?array
    {
        return Post::read($query,$searchTerm);
    }
    public static function deletePost($post_id)
    {
        return Post::delete($post_id);
    }


    public static function getCategories()
    {
        return Post::getCategories();
    }
    public static function addCategory($category)
    {
        return Post::addCategory($category);
    }
    public static function removeCategory($category)
    {
        return Post::removeCategory($category);
    }
    public static function updateCategory($category_old,$category_new)
    {
        return Post::updateCategory($category_old,$category_new);
    }



    public static function addLike($user_id, $post_id) {
        // Check if the user has already liked the post
        if (LikeModel::checkIfLiked($user_id, $post_id)) {
            return ['success' => false, 'message' => 'Already liked'];
        }

        // Add the like to the database
        $success = LikeModel::addLike($user_id, $post_id);

        return [
            'success' => $success,
            'like_count' => LikeModel::getLikeCount($post_id),
            'message' => $success ? 'Like added' : 'Failed to add like'
        ];
    }

    // Remove a like
    public static function removeLike($user_id, $post_id) {
        // Check if the user has not liked the post
        if (!LikeModel::checkIfLiked($user_id, $post_id)) {
            return ['success' => false, 'message' => 'Not liked yet'];
        }

        // Remove the like from the database
        $success = LikeModel::removeLike($user_id, $post_id);

        return [
            'success' => $success,
            'like_count' => LikeModel::getLikeCount($post_id),
            'message' => $success ? 'Like removed' : 'Failed to remove like'
        ];
    }
    public static function checkIfLiked($user_id, $post_id) {
        // Check if the user has liked the post
        $liked = LikeModel::checkIfLiked($user_id, $post_id);
    
        return [
            'success' => true,
            'liked' => $liked,
            'message' => $liked ? 'Post is liked' : 'Post is not liked'
        ];
    }


}