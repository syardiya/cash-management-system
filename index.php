<?php
/**
 * Index/Landing Page
 * Redirects to login or dashboard based on authentication status
 */

require_once 'config/config.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
