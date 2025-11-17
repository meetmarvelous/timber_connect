<?php
require_once 'includes/config.php';

echo "<h2>Database Inventory Debug</h2>";

try {
    // Check total inventory count
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM inventory");
    $total = $total_stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total inventory items: " . $total['total'] . "</p>";
    
    // Check available inventory
    $available_stmt = $pdo->query("SELECT COUNT(*) as available FROM inventory WHERE is_available = TRUE AND quantity_available > 0");
    $available = $available_stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Available inventory items: " . $available['available'] . "</p>";
    
    // Check marketers
    $marketers_stmt = $pdo->query("SELECT COUNT(*) as marketers FROM marketers WHERE verification_status = 'verified' AND is_active = TRUE");
    $marketers = $marketers_stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Verified active marketers: " . $marketers['marketers'] . "</p>";
    
    // Show some sample data
    echo "<h3>Sample Inventory Items:</h3>";
    $sample_stmt = $pdo->query("
        SELECT i.id, i.dimensions, i.price_per_unit, i.quantity_available, i.is_available,
               s.scientific_name, m.business_name, m.verification_status
        FROM inventory i
        JOIN species s ON i.species_id = s.id
        JOIN marketers m ON i.marketer_id = m.id
        LIMIT 10
    ");
    $sample_items = $sample_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Species</th><th>Business</th><th>Dimensions</th><th>Price</th><th>Qty</th><th>Available</th><th>Verified</th></tr>";
    foreach ($sample_items as $item) {
        echo "<tr>";
        echo "<td>" . $item['id'] . "</td>";
        echo "<td>" . $item['scientific_name'] . "</td>";
        echo "<td>" . $item['business_name'] . "</td>";
        echo "<td>" . $item['dimensions'] . "</td>";
        echo "<td>" . $item['price_per_unit'] . "</td>";
        echo "<td>" . $item['quantity_available'] . "</td>";
        echo "<td>" . ($item['is_available'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $item['verification_status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>