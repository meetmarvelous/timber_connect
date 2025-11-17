<?php
class Auth
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Marketer registration
    public function registerMarketer($data)
    {
        // Validate Nigerian phone
        if (!validateNigerianPhone($data['phone'])) {
            throw new Exception('Please provide a valid Nigerian phone number');
        }

        // Check if phone or email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM marketers WHERE phone = ? OR email = ?");
        $stmt->execute([$data['phone'], $data['email']]);
        if ($stmt->fetch()) {
            throw new Exception('A marketer with this phone or email already exists');
        }

        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert marketer
        $stmt = $this->pdo->prepare("INSERT INTO marketers 
            (business_name, owner_name, address, city, state, local_government, phone, email, password_hash, business_description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            sanitizeInput($data['business_name']),
            sanitizeInput($data['owner_name']),
            sanitizeInput($data['address']),
            sanitizeInput($data['city']),
            $data['state'],
            sanitizeInput($data['local_government']),
            formatNigerianPhone($data['phone']),
            sanitizeInput($data['email']),
            $password_hash,
            sanitizeInput($data['business_description'] ?? '')
        ]);

        return $this->pdo->lastInsertId();
    }

    // Marketer login
    public function loginMarketer($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM marketers WHERE email = ? AND is_active = TRUE");
        $stmt->execute([$email]);
        $marketer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($marketer && password_verify($password, $marketer['password_hash'])) {
            // Update last login
            $stmt = $this->pdo->prepare("UPDATE marketers SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$marketer['id']]);

            // Set session
            $_SESSION['marketer_id'] = $marketer['id'];
            $_SESSION['marketer_email'] = $marketer['email'];
            $_SESSION['marketer_business_name'] = $marketer['business_name'];
            $_SESSION['marketer_verified'] = $marketer['verification_status'] === 'verified';
            $_SESSION['user_role'] = 'marketer';

            return true;
        }

        return false;
    }

    // Admin login
    public function loginAdmin($username, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = TRUE");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Update last login
            $stmt = $this->pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);

            // Set session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['user_role'] = 'admin';

            return true;
        }

        return false;
    }

    // Buyer registration
    public function registerBuyer($data)
    {
        // Validate Nigerian phone
        if (!validateNigerianPhone($data['phone'])) {
            throw new Exception('Please provide a valid Nigerian phone number');
        }

        // Check if phone or email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM buyers WHERE phone = ? OR email = ?");
        $stmt->execute([$data['phone'], $data['email']]);
        if ($stmt->fetch()) {
            throw new Exception('A buyer with this phone or email already exists');
        }

        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert buyer
        $stmt = $this->pdo->prepare("INSERT INTO buyers (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            sanitizeInput($data['name']),
            sanitizeInput($data['email']),
            formatNigerianPhone($data['phone']),
            $password_hash,
        ]);

        return $this->pdo->lastInsertId();
    }

    // Buyer login (email or phone)
    public function loginBuyer($emailOrPhone, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM buyers WHERE (email = ? OR phone = ?) AND is_active = TRUE");
        $stmt->execute([$emailOrPhone, $emailOrPhone]);
        $buyer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($buyer && password_verify($password, $buyer['password_hash'])) {
            // Update last login
            $stmt = $this->pdo->prepare("UPDATE buyers SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$buyer['id']]);

            // Set session
            $_SESSION['buyer_id'] = $buyer['id'];
            $_SESSION['buyer_email'] = $buyer['email'];
            $_SESSION['buyer_name'] = $buyer['name'];
            $_SESSION['user_role'] = 'buyer';

            return true;
        }

        return false;
    }

    // Check if user is logged in
    public function isLoggedIn()
    {
        return isset($_SESSION['user_role']);
    }

    // Check if user is buyer
    public function isBuyer()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'buyer';
    }

    // Check if user is marketer
    public function isMarketer()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'marketer';
    }

    // Check if user is admin
    public function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    // Logout
    public function logout()
    {
        session_destroy();
        session_start();
    }

    // Require authentication
    public function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login.php');
            exit;
        }
    }

    // Require buyer role
    public function requireBuyer()
    {
        $this->requireAuth();
        if (!$this->isBuyer()) {
            header('Location: /unauthorized.php');
            exit;
        }
    }

    // Require marketer role
    public function requireMarketer()
    {
        $this->requireAuth();
        if (!$this->isMarketer()) {
            header('Location: /unauthorized.php');
            exit;
        }
    }

    // Require admin role
    public function requireAdmin()
    {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header('Location: /unauthorized.php');
            exit;
        }
    }
}

// Initialize auth
$auth = new Auth($pdo);
