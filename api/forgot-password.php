<?php
/**
 * Forgot Password API Endpoint
 * Generates reset link and sends email
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $email = sanitize_input($_POST['email'] ?? '');
    
    if (empty($email)) {
        echo json_encode(['status' => false, 'message' => 'Email is required']);
        exit;
    }
    
    $userObj = new User();
    $result = $userObj->generateResetToken($email);
    
    if ($result['status']) {
        $token = $result['token'];
        $resetLink = BASE_URL . "/reset-password.php?token=" . $token;
        
        // --- Email Logic ---
        $to = $email;
        $subject = "Reset Your Password - " . APP_NAME;
        $message = "
        <html>
        <head>
            <title>Reset Your Password</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                <h2 style='color: #4f46e5;'>Password Reset Request</h2>
                <p>Hello, <strong>" . $result['username'] . "</strong></p>
                <p>We received a request to reset your password. Click the button below to set a new password. This link will expire in 1 hour.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . $resetLink . "' style='background: #4f46e5; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                </div>
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p><a href='" . $resetLink . "'>" . $resetLink . "</a></p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #777;'>If you didn't request this reset, you can safely ignore this email.</p>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . APP_NAME . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n";
        
        // Attempt to send email
        $mailSent = @mail($to, $subject, $message, $headers);
        
        if ($mailSent) {
            echo json_encode(['status' => true, 'message' => 'Reset instructions have been sent to your email.']);
        } else {
            // Backup for free hosting: If mail fails, at least we generated it. 
            // In a real debug situation, we might want to tell the user the link if it's their own dev env.
            echo json_encode([
                'status' => true, 
                'message' => 'Reset link generated! (Note: Hosting mail server blocked outgoing email. Link: ' . $resetLink . ')' 
            ]);
        }
    } else {
        echo json_encode(['status' => false, 'message' => $result['message']]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'An error occurred.']);
}
