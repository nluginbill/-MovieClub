<?php include 'view/header.php'; ?>

<div class="container">
    <div class="row">
        <h3>Search for a friend's username</h3>
    </div>
    <form method="post" action="index.php">
        <input type="hidden" name="action" value="searchUsers">

        <div class="form-row align-items-center">
            <div class="col-sm">                
                <input type="text" class="form-control mb-2" name="usernameSearch" id="usernameSearch" 
                       placeholder="Search..." value="<?php if ($search !== "") echo $search ?>">
            </div>            

            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-2">Search</button>
            </div>
        </div>
    </form>
    <?php if (count($results) > 0) { ?>
        <div class="container">
            <?php foreach ($results as $result) { ?>

                <div class="row searchRow h-100 m-1">            
                    <div class="col-sm m-3 p-2">
                        <h4><?php echo $result->getUsername(); ?></h4>                        
                    </div>
                    <div class="col-sm m-auto p-2">
                        <form method="post" action="index.php">
                            <input type="hidden" name="action" value="friendInvite">
                            <input type="hidden" name="userIDto" value="<?php echo $result->getUserID(); ?>">
                            <?php if (in_array($result->getUserID(), $friends)) { ?>
                                <button type="submit" class="btn btn-primary btn-sm align-items-center float-right" disabled>friends</button>
                            <?php } else { ?>
                                <button type="submit" class="btn btn-primary btn-sm align-items-center float-right">Add</button>
                            <?php } ?>

                        </form>
                    </div>

                </div>
            <?php } ?>




        </div>
    <?php } ?>
</div>

<?php include 'view/footer.php'; ?>