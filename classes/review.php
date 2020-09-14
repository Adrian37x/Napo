<?php

    class Review {
        public $id;
        public $rating;
        public $comment;
        public $creatorId;  // who has created it
        public $ownerId;    // who has received it
        public $createTime;

        // get review by owner id
        public static function get_all_by_ownerId($ownerId) {
            global $db;

            $getAll = $db->prepare('SELECT id, rating, comment, creatorId, createTime FROM review WHERE ownerId = :value0 ORDER BY createTime DESC');
    
            $raw = $db->run($getAll, [$ownerId]);

            $reviews = [];

            if ($raw) {
                foreach ($raw as $row){
                    $review = new Review();
    
                    $review->id = $row["id"];
                    $review->rating = $row["rating"];
                    $review->comment = $row["comment"];
                    $review->creatorId = $row["creatorId"];
                    $review->ownerId = $ownerId;
                    $review->createTime = $row["createTime"];
    
                    array_push($reviews, $review);
                }
            }

            return $reviews;
        }

        // gets the average rating by owner id
        public static function get_average_rating($ownerId) {
            global $db;

            $getAll = $db->prepare('SELECT rating FROM review WHERE ownerId = :value0');
    
            $raw = $db->run($getAll, [$ownerId]);

            $ratingPoints = 0;
            $averageRating = 0;

            if ($raw) {
                foreach ($raw as $row){
                    $ratingPoints += $row["rating"];
                }

                $averageRating = $ratingPoints / count($raw);
            }

            return $averageRating;
        }

        // add review
        public static function add($rating, $comment, $creatorId, $ownerId) {
            global $db;

            $addReview = $db->prepare('INSERT INTO review (rating, comment, creatorId, ownerId, createTime) VALUES (:value0, :value1, :value2, :value3, :value4)');

            $db->run($addReview, [$rating, $comment, $creatorId, $ownerId, date('Y-m-d H:i:s')]);

            return true;
        }
    }

?>