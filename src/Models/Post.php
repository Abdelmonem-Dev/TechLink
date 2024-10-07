<?php
include_once __DIR__. '/DbConnection.php';

class Post
{
    private $_post_id;
    private $_user_id;
    private $_title;
    private $_subtitle;
    private $_description;
    private $_comments;
    private $_imageUrl; // If needed
    private $_userName;
    private $_userImageUrl;
    private $_descriptionUserImage;
    private $_postType;
    private $_createdAt;
    private $_updatedAt;
    private $images = []; // Add images property
    private $_like_count;
    private static $postCache = [];
    private $_budget;
    private $_deliveryTime;

    public static function fromDB(array $result): ?Post
    {
        $post = new self();
        $post->_post_id = $result['post_id'] ?? null;
        $post->_user_id = $result['user_id'] ?? null;
        $post->_title = $result['title'] ?? '';
        $post->_subtitle = $result['subtitle'] ?? '';
        $post->_description = $result['description'] ?? '';
        $post->_comments = $result['comments'] ?? '';
        $post->_imageUrl = $result['imageUrl'] ?? '';
        $post->_userName = $result['userName'] ?? '';
        $post->_userImageUrl = $result['userImageUrl'] ?? '';
        $post->_descriptionUserImage = $result['descriptionUserImage'];
        $post->_postType = $result['postType'] ?? '';
        $post->_createdAt = $result['createdAt'] ?? 0;
        $post->_updatedAt = $result['updatedAt'] ?? 0;
        $post->_budget = $result['budget'] ?? '';
        $post->_deliveryTime = $result['delivery_time'] ?? '';

        // Save to static array
        self::$postCache[$post->_post_id] = $post;

        return $post;
    }

    public static function create(Post $post): ?Post
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "INSERT INTO posts (user_id, title, subtitle, description, comments, imageUrl, userName, userImageUrl, postType,budget,delivery_time) 
                      VALUES (:user_id, :title, :subtitle, :description, :comments, :imageUrl, :userName, :userImageUrl, :postType,:budget,:delivery_time)";
            $stmt = $conn->prepare($query);

            $stmt->bindParam(':user_id', $post->_user_id);
            $stmt->bindParam(':title', $post->_title);
            $stmt->bindParam(':subtitle', $post->_subtitle);
            $stmt->bindParam(':description', $post->_description);
            $stmt->bindParam(':comments', $post->_comments);
            $stmt->bindParam(':imageUrl', $post->_imageUrl);
            $stmt->bindParam(':userName', $post->_userName);
            $stmt->bindParam(':userImageUrl', $post->_userImageUrl);
            $stmt->bindParam(':postType', $post->_postType);
            $stmt->bindParam(':budget', $post->_budget);
            $stmt->bindParam(':delivery_time', $post->_deliveryTime);

            $result = $stmt->execute();
            $post->_post_id = $conn->lastInsertId(); // Set the ID after insertion
            self::$postCache[$post->_post_id] = $post; // Save to static array
            DbConnection::Close();
            return $post;
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return null;
        }
    }

    public static function read($query, $searchTerm = ''): ?array
    {
        $conn = null;
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                error_log('Database connection failed.');
                return null;
            }
    
            $stmt = $conn->prepare($query);
    
            // Bind parameters for search term if provided
            if (!empty($searchTerm)) {
                if (is_array($searchTerm)) {
                    $searchTerm = implode(' ', $searchTerm); // Convert array to string if needed
                }
                $likeSearchTerm = '%' . $searchTerm . '%';
                $stmt->bindParam(':searchTerm', $likeSearchTerm, PDO::PARAM_STR);
            }
    
            $stmt->execute();
    
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                error_log("No posts returned from the database for query: " . $query);
                return null;
            }
    
            $posts = [];
            foreach ($results as $row) {
                $postId = $row['post_id'];
    
                if (!isset($posts[$postId])) {
                    $posts[$postId] = self::fromDB($row);
                    $posts[$postId]->images = []; // Initialize images array
                    $posts[$postId]->_like_count = $row['like_count']; // Set like count
                    $posts[$postId]->_userImageUrl = $row['userImageUrl']; // Set user image URL
                }
    
                if (!empty($row['image_url']) && !in_array($row['image_url'], $posts[$postId]->images)) {
                    $posts[$postId]->images[] = $row['image_url'];
                }
            }

            return array_values($posts); // Return posts as a numerically indexed array
    
        } catch (PDOException $e) {
            error_log("PDO Error reading posts: " . $e->getMessage() . " Query: " . $query);
            return null;
        } catch (Throwable $th) {
            error_log("General Error reading posts: " . $th->getMessage() . " Query: " . $query);
            return null;
        } finally {
            if ($conn) {
                $conn = null; // Close the connection
            }
        }
    }
    

    public static function getPostDetails(int $post_id): ?array
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "
            SELECT 
                posts.post_id,
                posts.user_id,
                posts.title,
                posts.subtitle,
                posts.description,
                posts.comments,
                posts.userName,
                posts.postType,
                posts.createdAt,
                posts.budget,
                posts.delivery_time,
                profile_images.imageUrl AS userImageUrl,
                profile_images.description AS descriptionUserImage,
                COUNT(post_likes.post_id) AS like_count
            FROM 
                posts
            LEFT JOIN 
                profile_images ON posts.user_id = profile_images.user_id
            LEFT JOIN
                post_likes ON posts.post_id = post_likes.post_id
            WHERE
                posts.post_id = :post_id
            GROUP BY 
                posts.post_id, posts.user_id, posts.title, posts.subtitle, posts.description, 
                posts.comments, posts.userName, posts.postType, posts.createdAt,posts.budget,
                posts.delivery_time,
                profile_images.imageUrl, profile_images.description
            ORDER BY 
                posts.createdAt DESC";
    
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
    
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
            DbConnection::Close();
            return $post;
    
        } catch (Throwable $th) {
            error_log("Error fetching post details: " . $th->getMessage());
            return null;
        }
    }
    public static function getPostImages(int $post_id): array
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "
            SELECT 
                imageUrl AS image_url,
                description AS descriptionPostImages
            FROM 
                post_images
            WHERE
                post_id = :post_id
            ORDER BY
                imageUrl";
    
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
    
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            DbConnection::Close();
            return $images;
    
        } catch (Throwable $th) {
            error_log("Error fetching post images: " . $th->getMessage());
            return [];
        }
    }
    

    public static function getByPostID(int $post_id): ?array
    {
        try {
            // Fetch post details
            $postDetails = self::getPostDetails($post_id);
            if ($postDetails === null) {
                return null; // Return null if post details could not be fetched
            }


            // Fetch post images
            $postImages = self::getPostImages($post_id);
    
            // Combine results
            $postDetails['images'] = $postImages;
            return $postDetails;
    
        } catch (Throwable $th) {
            error_log("Error fetching post: " . $th->getMessage());
            return null; // Return null if an error occurs
        }
    }
    
    public static function update(Post $post): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "UPDATE Posts SET
                        user_id = :user_id,
                        title = :title,
                        subtitle = :subtitle,
                        description = :description,
                        comments = :comments,
                        imageUrl = :imageUrl,
                        userName = :userName,
                        userImageUrl = :userImageUrl,
                        postType = :postType,
                        createdAt = :createdAt,
                        updatedAt = :updatedAt
                      WHERE post_id = :post_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':post_id', $post->_post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $post->_user_id);
            $stmt->bindParam(':title', $post->_title);
            $stmt->bindParam(':subtitle', $post->_subtitle);
            $stmt->bindParam(':description', $post->_description);
            $stmt->bindParam(':comments', $post->_comments);
            $stmt->bindParam(':imageUrl', $post->_imageUrl);
            $stmt->bindParam(':userName', $post->_userName);
            $stmt->bindParam(':userImageUrl', $post->_userImageUrl);
            $stmt->bindParam(':postType', $post->_postType);
            $stmt->bindParam(':createdAt', $post->_createdAt);
            $stmt->bindParam(':updatedAt', $post->_updatedAt);
            
            $result = $stmt->execute();
            // Update cache
            self::$postCache[$post->_post_id] = $post;
            DbConnection::Close(); // Ensure the connection is closed
            return $result;

        } catch (Throwable $th) {
            error_log("Error updating post: " . $th->getMessage());
            return false;
        }
    }

    public static function delete(int $id): bool
{
    try {
        $conn = DbConnection::getConnection();
        $conn->beginTransaction(); // Start transaction



        // Delete related notifications
        $query = "DELETE FROM notifications WHERE post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // Delete related post_images
        $query = "DELETE FROM post_images WHERE post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete related post_likes
        $query = "DELETE FROM post_likes WHERE post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the post
        $query = "DELETE FROM posts WHERE post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        // Commit transaction if all deletions are successful
        if ($result) {
            $conn->commit();
            unset(self::$postCache[$id]); // Remove from cache
            DbConnection::Close(); // Ensure the connection is closed
            return true;
        } else {
            $conn->rollBack(); // Roll back transaction if something fails
            return false;
        }

    } catch (Throwable $th) {
        $conn->rollBack(); // Roll back transaction in case of an error
        error_log("Error deleting post: " . $th->getMessage());
        return false;
    }
}

public static function getCategories() {
    $conn = DbConnection::getConnection();
    $query = "SELECT category FROM categories";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns an array of categories
}
public static function addCategory($category) {
    $conn = DbConnection::getConnection();
    $query = "INSERT INTO categories (category) VALUES (:category)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category', $category);
    $stmt->execute();
}
public static function removeCategory($category) {
    $conn = DbConnection::getConnection();
    $query = "DELETE FROM categories WHERE category = :category";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category', $category);
    $stmt->execute();
}
public static function updateCategory($oldCategory, $newCategory) {
    $conn = DbConnection::getConnection();
    $query = "UPDATE categories SET category = :newCategory WHERE category = :oldCategory";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':newCategory', $newCategory);
    $stmt->bindParam(':oldCategory', $oldCategory);
    $stmt->execute();
}


    public function getPostId(): ?int
    {
        return $this->_post_id;
    }

    public function setPostId(int $id): void
    {
        $this->_post_id = $id;
    }

    public function getUserId(): ?int
    {
        return $this->_user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->_user_id = $user_id;
    }

    public function getTitle(): ?string
    {
        return $this->_title;
    }

    public function setTitle(string $title): void
    {
        $this->_title = $title;
    }

    public function getSubtitle(): ?string
    {
        return $this->_subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->_subtitle = $subtitle;
    }

    public function getDescription(): ?string
    {
        return $this->_description;
    }

    public function setDescription(string $description): void
    {
        $this->_description = $description;
    }

    public function getComments(): ?string
    {
        return $this->_comments;
    }

    public function setComments(string $comments): void
    {
        $this->_comments = $comments;
    }

    public function getImageUrl(): ?string
    {
        return $this->_imageUrl;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->_imageUrl = $imageUrl;
    }

    public function getUserName(): ?string
    {
        return $this->_userName;
    }

    public function setUserName(string $userName): void
    {
        $this->_userName = $userName;
    }

    public function getUserImageUrl(): ?string
    {
        return $this->_userImageUrl;
    }

    public function setUserImageUrl(string $userImageUrl): void
    {
        $this->_userImageUrl = $userImageUrl;
    }
    public function getDescriptionUserImage(): ?string
    {
        return $this->_descriptionUserImage;
    }

    public function setDescriptionUserImage(string $DescriptionUserImage): void
    {
        $this->_descriptionUserImage = $DescriptionUserImage;
    }
    
    public function getPostType(): ?string
    {
        return $this->_postType;
    }

    public function setPostType(string $postType): void
    {
        $this->_postType = $postType;
    }

    public function getCreatedAt(): ?string
    {
        return $this->_createdAt;
    }

    public function setCreatedAt($createdAt): void
    {
        $this->_createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->_updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->_updatedAt = $updatedAt;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }
    public function getLike_count(): int
    {
        return $this->_like_count;
    }

    public function setLike_count(int $Like_count)
    {
        $this->_like_count = $Like_count;
    }


    public function getbudget()
    {
        return $this->_budget;
    }

    public function setbudget($budget)
    {
        $this->_budget = $budget;
    }
    public function getdeliveryTime()
    {
        return $this->_deliveryTime;
    }

    public function setdeliveryTime( $deliveryTime)
    {
        $this->_deliveryTime = $deliveryTime;
    }
}


class LikeModel {
    // Function to add a like
    public static function addLike($user_id, $post_id) {
        $conn = DbConnection::getConnection();
        $query = "INSERT INTO post_likes (user_id, post_id) VALUES (:user_id, :post_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);

        return $stmt->execute(); // This will return true if the insertion is successful
    }

    // Function to remove a like
    public static function removeLike($user_id, $post_id) {
        $conn = DbConnection::getConnection();
        $query = "DELETE FROM post_likes WHERE user_id = :user_id AND post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);

        return $stmt->execute(); // This will return true if the deletion is successful
    }

    // Function to check if the user already liked the post
    public static function checkIfLiked($user_id, $post_id) {
        $conn = DbConnection::getConnection();
        $query = "SELECT COUNT(*) FROM post_likes WHERE user_id = :user_id AND post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0; // Returns true if the user has liked the post, otherwise false
    }

    // Function to get the total like count for a post
    public static function getLikeCount($post_id) {
        $conn = DbConnection::getConnection();
        $query = "SELECT COUNT(*) FROM post_likes WHERE post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        return $stmt->fetchColumn(); // Returns the number of likes for the post
    }
}


class CommentModel {
    // public static function getComments(int $postId): array
    // {
    //     try {
    //         $conn = DbConnection::getConnection();
    //         $query = "
    //             SELECT 
    //                 comment_id, 
    //                 post_id, 
    //                 user_id, 
    //                 user_name, 
    //                 user_image_url, 
    //                 comment_text, 
    //                 like_count, 
    //                 created_at, 
    //                 parent_comment_id
    //             FROM 
    //                 post_comments 
    //             WHERE 
    //                 post_id = :post_id
    //             ORDER BY 
    //                 created_at ASC";
            
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    //         $stmt->execute();
            
    //         $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //         // Organize comments into a hierarchical structure
    //         $commentTree = [];
    //         $commentMap = [];
    
    //         foreach ($comments as $comment) {
    //             $commentId = $comment['comment_id'];
    //             $parentId = $comment['parent_comment_id'] ?? null;
    
    //             // Prevent the comment from being its own parent or re-adding itself to the tree
    //             if ($commentId === $parentId) {
    //                 error_log("Comment ID $commentId cannot be its own parent.");
    //                 continue;
    //             }
    
    //             // Ensure that replies array is initialized for each comment
    //             $comment['replies'] = [];
    
    //             // Store comment in the comment map
    //             if (!isset($commentMap[$commentId])) {
    //                 $commentMap[$commentId] = $comment;
    //             }
    
    //             if ($parentId === null) {
    //                 // Top-level comment, add to the comment tree
    //                 $commentTree[] = $commentMap[$commentId];
    //             } else {
    //                 // Reply, ensure the parent exists and add this comment as a reply
    //                 if (isset($commentMap[$parentId])) {
    //                     if (!isset($commentMap[$parentId]['replies'])) {
    //                         $commentMap[$parentId]['replies'] = [];
    //                     }
    //                     $commentMap[$parentId]['replies'][] = $commentMap[$commentId];
    //                 } else {
    //                     // Parent does not yet exist, create a placeholder in the map for it
    //                     $commentMap[$parentId] = ['replies' => [$commentMap[$commentId]]];
    //                 }
    //             }
    //         }
    
    //         // Debug: Print the full tree after processing all comments
    //         error_log("Final comment tree: " . print_r($commentTree, true));
    //         return $commentTree; // Return the top-level comments, with replies nested
    
    //     } catch (PDOException $e) {
    //         error_log("PDO Error fetching comments: " . $e->getMessage());
    //         return [];
    //     } catch (Throwable $th) {
    //         error_log("General Error fetching comments: " . $th->getMessage());
    //         return [];
    //     } finally {
    //         if (isset($conn)) {
    //             $conn = null; // Close the connection
    //         }
    //     }
    // }
    

    public static function AddComment($post_id, $user_id, $user_name, $comment_text, $parent_comment_id = null) {
        try {
            $conn = DbConnection::getConnection();
            
            // Fetch user image URL from profile_images table
            $query = "SELECT imageUrl FROM profile_images WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_image_url = $stmt->fetchColumn();
            
            if ($user_image_url === false) {
                // Default image if not found
                $user_image_url = 'default-user.png';
            }
            
            // Insert the comment
            $query = "INSERT INTO post_comments (post_id, user_id, user_name, user_image_url, comment_text, parent_comment_id) 
                      VALUES (:post_id, :user_id, :user_name, :user_image_url, :comment_text, :parent_comment_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_image_url', $user_image_url);
            $stmt->bindParam(':comment_text', $comment_text);
            $stmt->bindParam(':parent_comment_id', $parent_comment_id, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            DbConnection::Close(); // Ensure the connection is closed
            return $result; // Returns true if insertion is successful

        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        } catch (Throwable $th) {
            error_log("General Error: " . $th->getMessage());
            return false;
        }
    }
}