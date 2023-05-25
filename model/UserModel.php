<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class UserModel extends Database
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getUsers($limit)
    {
        return $this->select("SELECT * FROM users ORDER BY user_id ASC LIMIT ?", ["i", $limit]);
    }
    public function getUserInfo($email)
    {
        $sql = "SELECT * FROM users WHERE user_email = ? LIMIT 1";
        return $this->selectFirstRow($sql, [
            's',
            $email
        ]);
    }
    public function register($email, $password, $display_name, $contact, $age)
    {
        $isEmailExist = filter_var($this->checkIfUserEmailExist($email), FILTER_VALIDATE_BOOLEAN);

        if($isEmailExist) throw new InvalidArgumentException('This email has been registered! Please use another account');

        $insertSql = "INSERT INTO users(user_display_name, user_email, user_password, user_age, user_contact) VALUES(?, ?, ?, ?, ?)";

        return $this->insert($insertSql, [
            'sssis',
            $display_name,
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            $age,
            $contact
        ]);
    }
    public function changePassword($email, $password) 
    {
        $sql = "UPDATE users SET user_password = ? WHERE user_email = ?";
        return $this->update($sql, [
            'ss',
            password_hash($password, PASSWORD_BCRYPT),
            $email
        ]);
    }
    public function checkIfUserEmailExist($email)
    {
        $sql = "SELECT COUNT(*) = 1 FROM users WHERE user_email = ?";
        return $this->execScalar($sql, [
            's',
            $email
        ]);
    }
    public function getUserIdFromToken($jwt)
    {
        $decoded = JWT::decode($jwt, new Key(SECRET_KEY, JWT_KEY_ALGO));
        return $decoded->data->user_id;
    }
    public function getUserIdFromSession()
    {
        $userId = (!is_null($_SESSION) && isset($_SESSION['user_id']) && $_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        return $userId;
    }
    public function getJWTToken($userId, $email)
    {
        $issued_time = new DateTimeImmutable();
        $p = array(
            'iss' => ISSUER,
            'aud' => AUDIENCE,
            'iat' => $issued_time->getTimestamp(),
            'nbf' => $issued_time->getTimestamp(),
            'exp' => $issued_time->modify('+'.JWT_EXPIRY.' minutes')->getTimestamp(),
            'data' => [
                'user_id' => $userId,
                'user_email' => $email
            ]
        );

        $token = JWT::encode($p, SECRET_KEY, JWT_KEY_ALGO);
        return $token;
    }
    public function isUserLoggedIn($jwt)
    {
        try
        {
            $now = new DateTimeImmutable();
            $decoded = JWT::decode($jwt, new Key(SECRET_KEY, JWT_KEY_ALGO));
    
            if($decoded->iss !== ISSUER || 
                $decoded->nbf > $now->getTimestamp() || 
                $decoded->exp < $now->getTimestamp())
            {
                throw new Exception();
            }
            return true;
        }
        catch(Exception $e) 
        {
            return false;
        }
    }
}