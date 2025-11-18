<?php
// scripts/create_sample_payment.php
// Creates sample customer, product, order and a pending payment. Outputs payment id and reference.
require_once __DIR__ . '/../includes/config.php';

function out($v){ echo $v . PHP_EOL; }

try {
    // create customer
    $email = 'customer+sample@local';
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $hash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (name,email,password,role,is_active,created_at) VALUES (:name,:email,:pass,"customer",1,NOW())');
        $stmt->execute([':name' => 'Sample Customer', ':email' => $email, ':pass' => $hash]);
        $customerId = $conn->lastInsertId();
        out("Created customer id: $customerId");
    } else {
        $customerId = $row['id'];
        out("Using existing customer id: $customerId");
    }

    // create product
    $stmt = $conn->prepare('SELECT id FROM products WHERE title = :t');
    $stmt->execute([':t' => 'Sample Product']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $stmt = $conn->prepare('INSERT INTO products (title,slug,description,price,stock,is_active,created_at) VALUES (:t,:s,:d,1000,10,1,NOW())');
        $stmt->execute([':t' => 'Sample Product', ':s' => 'sample-product', ':d' => 'Product for testing']);
        $productId = $conn->lastInsertId();
        out("Created product id: $productId");
    } else {
        $productId = $row['id'];
        out("Using existing product id: $productId");
    }

    // create order
    $stmt = $conn->prepare('INSERT INTO orders (user_id,total_amount,shipping_method,status,created_at) VALUES (:uid,:total,:ship,:status,NOW())');
    $stmt->execute([':uid' => $customerId, ':total' => 1000.00, ':ship' => 'standard', ':status' => 'pending']);
    $orderId = $conn->lastInsertId();
    $stmt = $conn->prepare('INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (:oid,:pid,:qty,:price)');
    $stmt->execute([':oid' => $orderId, ':pid' => $productId, ':qty' => 1, ':price' => 1000.00]);
    out("Created order id: $orderId");

    // create payment pending
    $reference = 'TEST-' . time();
    $stmt = $conn->prepare('INSERT INTO payments (reference,order_id,user_id,amount,method,status,created_at) VALUES (:ref,:oid,:uid,:amt,:method,:status,NOW())');
    $stmt->execute([':ref' => $reference, ':oid' => $orderId, ':uid' => $customerId, ':amt' => 1000.00, ':method' => 'transfer', ':status' => 'pending']);
    $paymentId = $conn->lastInsertId();
    out("Created payment id: $paymentId with reference: $reference");

    echo json_encode(['payment_id' => (int)$paymentId, 'reference' => $reference]) . PHP_EOL;
} catch (Exception $e) {
    error_log('create_sample_payment error: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]) . PHP_EOL;
}
