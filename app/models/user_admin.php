<?php
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;

/**
 * Description of user
 *
 * @author NhatTieu
 */
class UserAdmin extends BaseModel
{
    use HasRole;

    protected $table = 'user_admin';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }


    public function createUser($username, $password, $power =1){
        $UserAdmin = new UserAdmin;
        $UserAdmin->username = $username;
        $UserAdmin->password = $password;
        $UserAdmin->lv = $power;
        $UserAdmin->save();
        return $UserAdmin->id;
    }
    public function getByUserName($username){
        return UserAdmin::where('username', '=', $username)->first();

    }
    // public function createUser($firstname, $lastname, $password, $email, $name, $verifyCode, $accountType, $city = null)
    // {
    //     $stmt = $this->db->prepare("INSERT INTO user (first_name, last_name, email, name, password, verify_code, account_type, dateline, last_logged, city) VALUES (:first_name, :last_name, :email, :name, :password, :verify_code, :account_type, :dateline, :last_logged, :city)");
    //     $inputParam = array(
    //         'first_name' => $firstname,
    //         'last_name' => $lastname,
    //         'password' => $password,
    //         'email' => $email,
    //         'name' => $name,
    //         'verify_code' => $verifyCode,
    //         'account_type' => $accountType,
    //         'dateline' => time(),
    //         'last_logged' => time(),
    //         'city' => $city
    //     );

    //     $exec = $stmt->execute($inputParam);

    //     if ($exec) {
    //         //$this->redis->hset('user:' . $this->db->lastInsertId(), 'first_name', $firstname);
    //         //$this->redis->hset('user:' . $this->db->lastInsertId(), 'last_name', $lastname);
    //         $id = $this->db->lastInsertId();
    //         $inputParam['user_id'] = $id;
    //         $userCodeTemp = md5('ttsid' . $id);
    //         $userCode = StringUtils::cut_string($userCodeTemp, 10);

    //         if ($this->checkUserCodeExists($userCode)) {
    //             $userCode .= 'a' . rand(0, 100);
    //         }

    //         $inputParam['user_code'] = $userCode;
    //         $this->redis->hmset('user:' . $id, $inputParam);
    //         $this->updateUserCode($id, $userCode);
    //         return $id;
    //     } else {
    //         return false;
    //     }
    // }

    
}
