<?php
/**
 * Success Stories Page
 * Redirects to blog page with success stories category filter
 */

// Redirect to blog with success stories filter
// If you have a specific category ID for success stories, add it here
header('Location: blog.php?category=success-stories');
exit;
