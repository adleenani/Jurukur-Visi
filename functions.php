<?php
// Input sanitization for different contexts
function sanitizeInput($data, $context = 'html')
{
    $data = trim($data);

    switch ($context) {
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        case 'sql':
            return $data; // Prepared statements handle SQL escaping
        case 'attribute':
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        case 'js':
            return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        default:
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// Secure password hashing
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// CSRF protection functions
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Secure redirect
function redirect($url)
{
    if (!headers_sent()) {
        header("Location: $url", true, 303);
        exit();
    }
    echo '<script>window.location.href="' . htmlspecialchars($url) . '";</script>';
    exit();
}

// Authentication checks
function isLoggedIn()
{
    return isset($_SESSION['user_id'], $_SESSION['ip']) && $_SESSION['ip'] === $_SERVER['REMOTE_ADDR'];
}


// Database query helper with prepared statements
function dbQuery($sql, $params = [])
{
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        throw new Exception("Database operation failed");
    }
}


// Additional security checks (recommended)
function requireAdmin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login first";
        redirect('admin_login.php');
    }
    
    if ($_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = "You don't have sufficient privileges";
        redirect('home_public.php');
    }
}
?>