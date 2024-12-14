<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if needed
session_start();

// Include database connection
require_once("include/config.php");

// Ensure no output before JSON response
ob_clean(); // Clear any previous output
header('Content-Type: application/json');

try {
    // Get the POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }
    
    // Pagination parameters
    $page = isset($data['page']) ? (int)$data['page'] : 1;
    $itemsPerPage = 8; // Number of properties per page
    $offset = ($page - 1) * $itemsPerPage;
    
    // First, get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM property WHERE 1=1";
    $whereClauses = array();
    $params = array();
    $types = "";

    // Add filters to WHERE clause
    if (!empty($data['keyword'])) {
        $whereClauses[] = "(property_name LIKE ? OR description LIKE ?)";
        $params[] = "%{$data['keyword']}%";
        $params[] = "%{$data['keyword']}%";
        $types .= "ss";
    }

    if (!empty($data['type'])) {
        $whereClauses[] = "type = ?";
        $params[] = $data['type'];
        $types .= "s";
    }

    // Add other filters similarly...
    if (!empty($data['status'])) {
        $whereClauses[] = "status = ?";
        $params[] = $data['status'];
        $types .= "s";
    }

    if (!empty($data['city'])) {
        $whereClauses[] = "city LIKE ?";
        $params[] = "%{$data['city']}%";
        $types .= "s";
    }

    if (!empty($data['price'])) {
        $whereClauses[] = "property_price <= ?";
        $params[] = (float)$data['price'];
        $types .= "d";
    }

    if (!empty($data['min_bed'])) {
        $whereClauses[] = "min_bed >= ?";
        $params[] = (int)$data['min_bed'];
        $types .= "i";
    }

    if (!empty($data['min_bath'])) {
        $whereClauses[] = "min_baths >= ?";
        $params[] = (int)$data['min_bath'];
        $types .= "i";
    }

    if (!empty($data['min_kitchen'])) {
        $whereClauses[] = "min_kitchen >= ?";
        $params[] = (int)$data['min_kitchen'];
        $types .= "i";
    }

    if (!empty($data['area'])) {
        $whereClauses[] = "property_geo >= ?";
        $params[] = (float)$data['area'];
        $types .= "d";
    }

    // Combine WHERE clauses
    if (!empty($whereClauses)) {
        $countSql .= " AND " . implode(" AND ", $whereClauses);
    }

    // Get total count
    $countStmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalResult = $countStmt->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    $totalPages = ceil($total / $itemsPerPage);

    // Main query for properties with pagination
    $sql = "SELECT p_id, property_name, status, property_geo, property_price, main_image, 
            min_bed, min_baths, min_kitchen 
            FROM property WHERE 1=1";

    if (!empty($whereClauses)) {
        $sql .= " AND " . implode(" AND ", $whereClauses);
    }

    $sql .= " ORDER BY property_price DESC LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $itemsPerPage;
    $params[] = $offset;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $properties = array();
    
    while ($row = $result->fetch_assoc()) {
        if ($row['main_image']) {
            $row['main_image'] = base64_encode($row['main_image']);
        } else {
            $row['main_image'] = '';
        }
        $properties[] = $row;
    }

    $stmt->close();
    $conn->close();

    die(json_encode([
        'success' => true,
        'data' => $properties,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total,
            'items_per_page' => $itemsPerPage
        ]
    ]));

} catch (Exception $e) {
    // Log the error
    error_log("Filter Properties Error: " . $e->getMessage());
    
    // Send error response and exit
    die(json_encode([
        'success' => false,
        'error' => 'An error occurred while filtering properties',
        'debug' => $e->getMessage()
    ]));
}
?>