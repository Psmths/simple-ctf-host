<?php
    /* Query to see if a user by user_id exists */
    function query_user_exists($user_id) {
        $sql = 'SELECT * FROM accounts WHERE id=:user_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /* Query to see if a user by user_id exists */
    function query_challenge_exists($challenge_id) {
        $sql = 'SELECT * FROM challenges WHERE id=:challenge_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('challenge_id', $challenge_id, PDO::PARAM_INT);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /* Query to get the challenge points
       (calculated by the difficulty rating!)
    */
    function query_challenge_points($challenge_id) {
        // First check if the challenge exists
        if (!query_challenge_exists($challenge_id)) return;

        // Perform DB operation to get challenge difficulty
        $sql = 'SELECT difficulty FROM challenges WHERE id=:challenge_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('challenge_id', $challenge_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        $challenge_difficulty = $result["difficulty"];

        // Calculate the point value for this challenge
        return 100 * pow($challenge_difficulty, 2);
    }

    /* Query to get an array of all the challenges
       a user has solved.
    */
    function query_user_solves($user_id) {
        // First check if the user exists
        if (!query_user_exists($user_id)) return;

        // Perform DB operation to get solved challenge ids
        $sql = 'SELECT challenge_id FROM solves WHERE user_id=:user_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result;
    }

    /* Query to get a count of how many times a challenge was
       solved.
    */
    function query_challenge_solves($challenge_id) {
        // First check if the user exists
        if (!query_challenge_exists($challenge_id)) return;

        // Perform DB operation to get solved challenge ids
        $sql = 'SELECT * FROM solves WHERE challenge_id=:challenge_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('challenge_id', $challenge_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return sizeof($result);
    }

    /* Query to check if user_id has completed challenge_id
    */
    function query_user_solve_status($user_id, $challenge_id) {
        // First check if the user and challenge both exist
        if (!query_user_exists($user_id)) return;
        if (!query_challenge_exists($challenge_id)) return;

        // Perform DB operation to get solved challenge ids
        $sql = 'SELECT * FROM solves WHERE user_id=:user_id AND challenge_id=:challenge_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue('challenge_id', $challenge_id, PDO::PARAM_INT);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /* Query to calculate a user's total points */
    function calc_user_points($user_id) {
        // Get a list of the user's solved challenges
        $user_solved_challenges = query_user_solves($user_id);
        $user_points = 0;
        foreach ($user_solved_challenges as $challenge_id) {
            $user_points += query_challenge_points($challenge_id);
        }
        return $user_points;
    }

    /* Get an array of all user IDs */
    function query_all_user_ids() {
        $sql = 'SELECT id FROM accounts';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result;
    }

    /* Get an array of all challenges */
    function query_all_challenges() {
        $sql = 'SELECT * FROM challenges';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /* Get a count of challenges */
    function get_num_challenges() {
        $sql = 'SELECT * FROM challenges';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return sizeof($result);
    }

    /* Get a count of accounts */
    function get_num_accounts() {
        $sql = 'SELECT * FROM accounts';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return sizeof($result);
    }

    /* Get a count of solves */
    function get_num_solves() {
        $sql = 'SELECT * FROM solves';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return sizeof($result);
    }

    /* Get a list of admins */
    function get_admins() {
        $sql = 'SELECT username FROM accounts where is_admin=1';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /* Check to see if a user is an admin by their user ID */
    function query_is_admin($user_id) {
        $sql = 'SELECT is_admin FROM accounts WHERE id=:user_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /* Query to get a sorted leaderboard array */
    function get_leaderboard_array() {
        $leaderboard_array = array();

        // Get an array of all user IDs
        $user_id_array = query_all_user_ids();

        foreach ($user_id_array as $user_id) {
            // Check if we want to hide administrators
            if (HIDE_ADMINS_FROM_LEADERBOARD) {
                if (query_is_admin($user_id)) continue; 
            }
            array_push($leaderboard_array, array(
                "user_id" => $user_id, 
                "score" => calc_user_points($user_id))
            );
        }

        // Sort the leaderboard by score
        array_multisort(array_column($leaderboard_array, "score"), SORT_DESC, $leaderboard_array);

        return $leaderboard_array;
    }

    /* Query to get a username from a user ID */
    function user_id_to_name($user_id) {
        $sql = 'SELECT username FROM accounts WHERE id=:user_id';
        $statement = db()->prepare($sql);
        $statement->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return $result["username"];
    }

    /* Query to get a challenge ID from its name */
    function challenge_name_to_id($challenge_name) {
        $sql = 'SELECT id FROM challenges WHERE name=:challenge_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('challenge_name', $challenge_name, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();
        return $result["id"];
    }

    /* Query to get unique categories */
    function get_categories_array() {
        $sql = 'SELECT category FROM challenges';
        $statement = db()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        // Remove the duplicates returned
        $result = array_unique($result);
        return $result;
    }

    /* Query to get all challenges from a category */
    function get_category_challenges($category) {
        $category_array = array();
        $sql = 'SELECT * FROM challenges WHERE category=:category';
        $statement = db()->prepare($sql);
        $statement->bindValue('category', $category, PDO::PARAM_STR);
        $statement->execute();
        $category_array = $statement->fetchAll();
        // Sort by difficulty
        array_multisort(array_column($category_array, "difficulty"), SORT_ASC, $category_array);
        return $category_array;
    }

    /* Query to build the challenge deck */
    function get_challenge_array() {
        $challenge_array = array();

        // Get an array of all the challenge types
        $categories = get_categories_array();
        foreach ($categories as $category) {
            array_push($challenge_array, array(
                "category" => $category, 
                "challenges" => get_category_challenges($category))
            );
        }
        return $challenge_array;
    }
?>