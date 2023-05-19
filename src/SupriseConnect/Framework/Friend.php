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
}
