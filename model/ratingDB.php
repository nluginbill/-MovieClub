<?php

class RatingDB {

    public static function getRatingByID($ratingID) {
        $db = Database::getDB();
        $query = 'SELECT * FROM ratings
                  WHERE ratingID = :ratingID';
        $statement = $db->prepare($query);
        $statement->bindValue(":ratingID", $ratingID);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();


        $rating = new Rating($row['userID'], $row['tmdbID'], $row['rating']);
        $rating->setRatingID($ratingID);
        $rating->setRatingDate($row['ratingDate']);
        return $rating;
    }

    public static function getUserRatingByTmdbID($user, $tmdbID) {
        $db = Database::getDB();
        $query = 'SELECT * FROM ratings
                  WHERE userID = :userID
                  AND tmdbID = :tmdbID';
        $statement = $db->prepare($query);
        $statement->bindValue(":userID", $user->getUserID());
        $statement->bindValue(":tmdbID", $tmdbID);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();

        $movie = MovieDB::getMovie($tmdbID);
        $rating = null;
        if (!empty($row)) {
            $rating = new Rating($row['userID'], $row['tmdbID'], $row['rating'], $movie);
            $rating->setRatingID($row['ratingID']);
            $rating->setRatingDate($row['ratingDate']);
        }
        return $rating;
    }

    public static function addRating($rating) {
        $db = Database::getDB();

        $userID = $rating->getUserID();
        $tmdbID = $rating->getTmdbID();
        $newRating = $rating->getRating();
        $movie = $rating->getMovie();
        $genres = $movie->getGenres();

        //if a movie with this tmdb hasn't been entered into the database yet,
        //then enter it
        if (MovieDB::getMovie($tmdbID) === false) {
            MovieDB::addMovie($movie);
        }

        try {
            $query = 'INSERT INTO ratings
                (userID, tmdbID, rating)
                    VALUES
                (:userID, :tmdbID, :rating)';
            $statement = $db->prepare($query);
            $statement->bindValue(':userID', $userID);
            $statement->bindValue(':tmdbID', $tmdbID);
            $statement->bindValue(':rating', $newRating);
            $statement->execute();
            $statement->closeCursor();
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            include("index.php");
            exit();
        }
    }

    public static function editRating($rating) {
        $db = Database::getDB();

        $userID = $rating->getUserID();
        $tmdbID = $rating->getTmdbID();
        $newRating = $rating->getRating();


        try {
            $query = 'UPDATE ratings
                SET rating = :rating
                WHERE userID = :userID
                AND tmdbID = :tmdbID';
            $statement = $db->prepare($query);
            $statement->bindValue(':userID', $userID);
            $statement->bindValue(':tmdbID', $tmdbID);
            $statement->bindValue(':rating', $newRating);
            $statement->execute();
            $statement->closeCursor();
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            include("index.php");
            exit();
        }
    }

    public static function getUserMovieRatings($user) {
        $db = Database::getDB();

        $query = 'SELECT * FROM ratings r
                  INNER JOIN movies m ON r.tmdbID = m.tmdbID
                  WHERE userID = :userID';
        $statement = $db->prepare($query);
        $statement->bindValue(":userID", $user->getUserID());
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        $ratings = null;
        foreach ($rows as $row) {
            $movie = MovieDB::getMovie($row['tmdbID']);
            $rating = new Rating($row['userID'], $row['tmdbID'], $row['rating'], $movie);
            $rating->setRatingID($row['ratingID']);
            $rating->setRatingDate($row['ratingDate']);

            $ratings[] = $rating;
        }

        return $ratings;
    }

    public static function getUserRatingsAndReviews($user) {
        $db = Database::getDB();

        $query = 'SELECT r.ratingID, r.userID, r.tmdbID, r.ratingDate, r.rating,
            m.title, m.overview, m.poster, re.reviewID, re.userID,
            re.reviewDate, re.review
            FROM ratings r 
            JOIN movies m ON r.tmdbID = m.tmdbID
            RIGHT JOIN reviews re ON r.tmdbID = re.tmdbID and r.userID = re.userID
            WHERE re.userID = :userID';
        $statement = $db->prepare($query);
        $statement->bindValue(":userID", $user->getUserID());
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        $ratingsAndReviews = array();
        foreach ($rows as $row) {
            $movie = MovieDB::getMovie($row['tmdbID']);
            $rating = new Rating($row['userID'], $row['tmdbID'], $row['rating'], $movie);
            $rating->setRatingID($row['ratingID']);
            $rating->setRatingDate($row['ratingDate']);
            $review = false;

            if (!is_null($row['reviewID'])) {
                $review = new Review($user->getUserID(), $row['tmdbID'], $row['review'], $movie);
                $review->setReviewID($row['reviewID']);
                $review->setReviewDate($row['reviewDate']);
            }

            $ratingAndReview['rating'] = $rating;
            $ratingAndReview['review'] = $review;
            $ratingsAndReviews[] = $ratingAndReview;
        }

        return $ratingsAndReviews;
    }

}
