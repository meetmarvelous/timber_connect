<?php
// This script helps find and fix path issues
$base_url = '/timber-connect';

$files = [
    'index.php',
    'login.php',
    'marketer-register.php',
    'about.php',
    'contact.php',
    'dashboard/index.php',
    'dashboard/marketers.php',
    'dashboard/species.php',
    'dashboard/marketer/index.php',
    'dashboard/marketer/inventory.php',
    'dashboard/marketer/inquiries.php',
    'marketplace/index.php',
    'marketplace/search.php',
    'marketplace/profile.php',
    'marketplace/inquiry.php',
    'species/directory.php',
    'species/profile.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Fix CSS links
        $content = str_replace('href="assets/css', 'href="' . $base_url . '/assets/css', $content);
        $content = str_replace('href="../assets/css', 'href="' . $base_url . '/assets/css', $content);
        $content = str_replace('href="../../assets/css', 'href="' . $base_url . '/assets/css', $content);
        
        // Fix JS links
        $content = str_replace('src="assets/js', 'src="' . $base_url . '/assets/js', $content);
        $content = str_replace('src="../assets/js', 'src="' . $base_url . '/assets/js', $content);
        $content = str_replace('src="../../assets/js', 'src="' . $base_url . '/assets/js', $content);
        
        // Fix image links
        $content = str_replace('src="assets/images', 'src="' . $base_url . '/assets/images', $content);
        $content = str_replace('src="../assets/images', 'src="' . $base_url . '/assets/images', $content);
        
        // Fix internal links
        $content = str_replace('href="/marketplace/', 'href="' . $base_url . '/marketplace/', $content);
        $content = str_replace('href="/dashboard/', 'href="' . $base_url . '/dashboard/', $content);
        $content = str_replace('href="/species/', 'href="' . $base_url . '/species/', $content);
        
        file_put_contents($file, $content);
        echo "Fixed: $file\n";
    }
}

echo "Path fixing completed!\n";
?>