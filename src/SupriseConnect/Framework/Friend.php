<?php

namespace SupriseConnect\Framework;

class Friend
{
    //add friend to database
    public function addFriend($user_id, $friend_id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO friends (user_id, friend_id) VALUES (:user_id, :friend_id)");
        $statement->bindValue(":user_id", $user_id);
        $statement->bindValue(":friend_id", $friend_id);
        $result = $statement->execute();
        return $result;
    }

    //checks if user is already friends with the other user
    public static function isFriend($user_id, $friend_id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM friends WHERE user_id = :user_id AND friend_id = :friend_id");
        $statement->bindValue(":user_id", $user_id);
        $statement->bindValue(":friend_id", $friend_id);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!empty($result)) {
            // The user is already friends with the other user
            return true;
        } else {
            // The user is not friends with the other user
            return false;
        }
    }

    //gets all friends of user and get all informatie of friends id from users table
    public static function getFriends($user_id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM friends WHERE user_id = :user_id");
        $statement->bindValue(":user_id", $user_id);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $friends = array();

        foreach ($result as $friend) {
            $statement = $conn->prepare("SELECT * FROM users WHERE id = :friend_id");
            $statement->bindValue(":friend_id", $friend['friend_id']);
            $statement->execute();
            $friend = $statement->fetch(\PDO::FETCH_ASSOC);
            array_push($friends, $friend);
        }

        return $friends;
    }
}
