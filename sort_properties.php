<?php
include("include/config.php");

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'created_at';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 8;
$offset = ($page - 1) * $results_per_page;

// Sanitize inputs
$allowed_orderby = ['created_at', 'property_price'];
$allowed_order = ['ASC', 'DESC'];

if (!in_array($orderby, $allowed_orderby)) {
    $orderby = 'created_at';
}
if (!in_array($order, $allowed_order)) {
    $order = 'ASC';
}

$sql = "SELECT * FROM property ORDER BY $orderby $order LIMIT $offset, $results_per_page";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Output the same HTML structure as in your main file
        include("property_card_template.php");
    }
} else {
    echo "<div class='col-sm-12 text-center'><p>No properties found</p></div>";
}
?>