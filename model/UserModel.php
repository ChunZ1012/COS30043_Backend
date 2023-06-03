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
    public function getUserAccountInfo($user_id)
    {
        $sql = "SELECT user_id AS accountUserID, user_display_name AS accountName, user_age AS accountAge, user_contact AS accountContact, user_email AS accountEmail, 
        (
            CASE 
                WHEN ISNULL(user_avatar) THEN NULL
                ELSE CONCAT('".PUBLIC_ASSETS_AVATAR_PATH."', user_avatar)
            END
        ) AS accountProfilePicture FROM users WHERE user_id = ? LIMIT 1";
        return $this->selectFirstRow($sql, [
            'i',
            $user_id
        ]);
    }
    public function getUserDeliveryInfos($user_id) {
        $sql = "SELECT id AS deliveryID, delivery_name AS deliveryName, delivery_address_1 AS deliveryAddress1, delivery_address_2 AS deliveryAddress2, delivery_contact AS deliveryContact, delivery_email AS deliveryEmail FROM users_delivery_information WHERE user_id = ?";
        return $this->select($sql, [
            'i',
            $user_id
        ]);
    }
    public function updateUserInfo($user_id, $data) 
    {
        $isAvatarExist = isset($_FILES['accountProfilePicture']);
        $sql = "UPDATE users SET user_display_name = ?, user_email = ?, user_age = ?, user_contact = ?". ($isAvatarExist ? ", user_avatar = ?" : "") ." WHERE user_id = ?";

        $avatarResult = [];
        if($isAvatarExist) 
        {
            $prevAvatarName = $this->execScalar("SELECT user_avatar FROM users WHERE user_id = ?", [
                'i',
                $user_id
            ]);
            $avatarResult = $this->checkAndSaveAvatar($prevAvatarName);
        }
        
        // Required params
        $bindParams = 'ssis';        
        $params = [
            $data['accountName'],
            $data['accountEmail'],
            $data['accountAge'],
            $data['accountContact']
        ];
        // Only push in if the avatar picture is set/ uploaded
        if($isAvatarExist && $avatarResult[0] == true) 
        {
            $bindParams .= 's';
            array_push($params, $avatarResult[1]);
        }
        // User Id param
        $bindParams .= 'i';
        array_push($params, $user_id);
        // Sql params
        $sqlParams = [$bindParams, ...$params];
        // Return result
        return $this->update($sql, $sqlParams);
    }
    public function addDeliveryInfo($user_id, $data)
    {
        $sql = "INSERT INTO users_delivery_information(user_id, delivery_name, delivery_address_1, delivery_address_2, delivery_contact, delivery_email) VALUES(?, ?, ?, ?, ?, ?);";

        return $this->update($sql, [
            'isssss',
            $user_id,
            $data['deliveryName'],
            $data['deliveryAddress1'],
            $data['deliveryAddress2'],
            $data['deliveryContact'],
            $data['deliveryEmail'],
        ]);
    }
    public function updateDeliveryInfo($user_id, $data)
    {
        $sql = "UPDATE users_delivery_information SET delivery_name = ?, delivery_address_1 = ?, delivery_address_2 = ?, delivery_contact = ?, delivery_email = ? WHERE user_id = ? AND id = ?;";

        return $this->update($sql, [
            'sssssii',
            $data['deliveryName'],
            $data['deliveryAddress1'],
            $data['deliveryAddress2'],
            $data['deliveryContact'],
            $data['deliveryEmail'],
            $user_id,
            $data['deliveryID']
        ]);
    }
    public function deleteDeliveryInfo($user_id, $delivery_id)
    {
        $sql = "DELETE FROM users_delivery_information WHERE user_id = ? AND id = ?";
        return $this->delete($sql, [
            'ii',
            $user_id,
            $delivery_id
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
    public function changePassword($user_id, $new_password)
    {
        $sql = "UPDATE users SET user_password = ? WHERE user_id = ?";
        return $this->update($sql, [
            'ss',
            password_hash($new_password, PASSWORD_BCRYPT),
            $user_id
        ]);
    }
    public function resetPassword($email, $password) 
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
    protected function checkAndSaveAvatar($prevAvatarName) 
    {
        $rnd = uniqid();
        $avatarFileName = null;

        $allowedExts = ['jpeg', 'jpg', 'png'];
        $file = $_FILES['accountProfilePicture'];
        $avatar = explode('.', $file['name']);
        $ext = end($avatar);
        
        $isMoved = false;

        if ((($file["type"] == "image/jpeg")
        || ($file["type"] == "image/jpg")
        || ($file["type"] == "image/x-png")
        || ($file["type"] == "image/png"))
        && in_array($ext, $allowedExts)) 
        {
            if($file['error'] > 0) return $avatarFileName;
            else
            {
                $isMoved = move_uploaded_file($file['tmp_name'], ASSETS_AVATAR_PATH.$rnd.'.'.$ext);
                if($isMoved) 
                {
                    $avatarFileName = $rnd.'.'.$ext;
                    try
                    {
                        $prevAvatarFilePath = ASSETS_AVATAR_PATH.$prevAvatarName;
                        if(is_file($prevAvatarFilePath) && file_exists($prevAvatarFilePath)) unlink($prevAvatarFilePath);
                    }
                    catch(Exception $e) {}
                }
            }
        }

        return array($isMoved, $avatarFileName);
    }
}