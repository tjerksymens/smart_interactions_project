<?php

namespace SupriseConnect\Framework;

class User
{
    private string $email;
    private string $username;
    private string $firstname;
    private string $lastname;
    private string $password;
    private string $confirmpassword;
    private string $image;


    public function setEmail($email)
    {
        if (empty($email)) {
            throw new \Exception("Email cannot be empty.");
        } else {
            $this->email = $email;
            return $this;
        }
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUsername($username)
    {
        if (empty($username)) {
            throw new \Exception("Username cannot be empty.");
        } else {
            $this->username = $username;
            return $this;
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setFirstname($firstname)
    {
        if (empty($firstname)) {
            throw new \Exception("Firstname cannot be empty.");
        } else {
            $this->firstname = $firstname;
            return $this;
        }
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        if (empty($lastname)) {
            throw new \Exception("Lastname cannot be empty.");
        } else {
            $this->lastname = $lastname;
            return $this;
        }
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setPassword($password)
    {
        if (strlen($password) < 8) {
            throw new \Exception("Password must be at least 8 characters.");
        } else {
            $options = [
                'cost' => 12,
            ];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);
            $this->password = $password;
            return $this;
        }
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setConfirmPassword($confirmpassword)
    {
        //confirm passwo
        if (empty($confirmpassword)) {
            throw new \Exception("Confirm password cannot be empty.");
        } else {
            $options = [
                'cost' => 12,
            ];
            $confirmpassword = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);
            $this->confirmpassword = $confirmpassword;
            return $this;
        }
    }

    public function getConfirmPassword()
    {
        return $this->confirmpassword;
    }

    public function getImage()
    {
        if (isset($this->image)) {
            return $this->image;
        } else {
            return null;
        }
    }

    public function setImage($image)
    {
        if (empty($image)) {
            throw new \Exception("Image cannot be empty.");
        } else {
            $this->image = $image;
            return $this;
        }
    }

    //saves user to database
    public function save()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO users (email, username, password, firstname, lastname, image) VALUES (:email, :username, :password, :firstname, :lastname, :image)");
        $statement->bindValue(":email", $this->email);
        $statement->bindValue(":username", $this->username);
        $statement->bindValue(":password", $this->password);
        $statement->bindValue(":firstname", $this->firstname);
        $statement->bindValue(":lastname", $this->lastname);
        $statement->bindValue(":image", $this->image);
        return $statement->execute();
    }

    //checks if email already exists
    public static function checkExistingEmail($email)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!empty($result)) {
            // The email already exists in the database
            return true;
        } else {
            // The email does not exist in the database
            return false;
        }
    }

    //checks if username already exists
    public static function checkExistingUsername($username)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!empty($result)) {
            // The username already exists in the database
            return true;
        } else {
            // The username does not exist in the database
            return false;
        }
    }

    //gets user id
    public static function getId($email)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result['id'];
    }

    //checks if user can login
    public function canLogin($username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new \Exception("Email and password are required.");
        } else {
            $conn = Db::getInstance();
            $statement = $conn->prepare("SELECT * FROM users WHERE email = :email OR username = :email");
            $statement->bindValue(":email", $username);
            $statement->execute();
            $user = $statement->fetch(\PDO::FETCH_ASSOC);
            $hash = $user['password'];

            if (password_verify($password, $hash)) {
                return true;
            } else {
                return false;
            }
        }
    }

    //sends location to database
    public static function updateLocation($latitude, $longitude, $userId)
    {
        try {
            // Create a PDO instance and establish the database connection
            $pdo = Db::getInstance();
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Prepare and execute the SQL statement to update the location data
            $query = "UPDATE users SET latitude = :latitude, longitude = :longitude WHERE id = :id";
            $statement = $pdo->prepare($query);
            $statement->bindParam(':latitude', $latitude);
            $statement->bindParam(':longitude', $longitude);
            $statement->bindParam(':id', $userId);
            $statement->execute();

            echo "Location data updated successfully";
        } catch (\PDOException $e) {
            echo "Error updating data in the database: " . $e->getMessage();
        }
    }

    // zoekt user op id
    public static function getUserById($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    // zoekt user op username of firstname of lastname
    public static function searchUsers($search)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users WHERE username LIKE :search OR firstname LIKE :search OR lastname LIKE :search");
        $statement->bindValue(":search", '%' . $search . '%');
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    //get friends username from users class
    public static function getFriendsInformation($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users INNER JOIN friends ON users.id = friends.user_id WHERE friends.friend_id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}
