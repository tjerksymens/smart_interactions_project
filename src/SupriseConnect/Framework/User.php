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

    public static function getId($email)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result['id'];
    }
}
