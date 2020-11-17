<?php include 'view/header.php'; ?>

<?php //  var_dump($movie) ?>

<div class="container">
    <div class="row">
    <div class="card m-10 mx-auto" style="width: 30rem;">
        <img class="card-img-top p-5"   src="https://image.tmdb.org/t/p/w500<?php echo $movie['poster_path']; ?>" alt="poster">
        <div class="card-body">
            <h5 class="card-title"><?php echo $movie['title']; ?></h5>
            <p><em>Released <?php echo date_format(date_create($movie['release_date']), 'M Y'); ?>
                </em></p>
            <p class="card-text"><?php echo $movie['overview']; ?></p>
            
            <form method="post" action="index.php">
                <input type="hidden" name="action" value="addRating">
                <input type="hidden" name="movieID" value="<?php echo $movie['id'] ?>">
                <input type="hidden" name="title" value="<?php echo $movie['title'] ?>">
                <input type="hidden" name="overview" value="<?php echo $movie['overview'] ?>">
                <input type="hidden" name="poster" value="<?php echo $movie['poster_path'] ?>">
                <?php $genreCounter = 0; ?>
                <?php for($genreCounter = 0; $genreCounter < count($movie['genre_ids']); $genreCounter++) { ?>
                    <input type="hidden" name="genre<?php echo $genreCounter ?>" value="<?php echo $movie['genre_ids'][$genreCounter] ?>">
                <?php } ?>
                <input type="hidden" name="genreCounter" value="<?php echo $genreCounter ?>">
                <select id="ddRating" name="rating">
                    <?php for($i = 0; $i < 10; $i++) { ?>
                        <option value="<?php echo $i + 1 ?>"><?php echo $i + 1 ?> stars</option>
                    <?php } ?>
                </select>
                <br>
                <input class="btn btn-outline-primary" type="submit" value="Submit">
            </form>
                      
            <a href="index.php?action=rater" class="btn btn-outline-secondary">Haven't seen</a>
        </div>
    </div>
</div>
</div>

<!--<img src="https://image.tmdb.org/t/p/w500
<?php // echo $movie['poster_path']; ?>
 " alt="poster">-->

<?php include 'view/footer.php'; ?>
