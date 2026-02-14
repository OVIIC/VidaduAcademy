<?php
try {
    $host = '127.0.0.1';
    $db   = 'vidadu_academy';
    $user = 'sail';
    $pass = 'password';
    $port = 3306;
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    echo "Connecting to $dsn...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully.\n";

    echo "Fetching latest course...\n";
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC LIMIT 1");
    $course = $stmt->fetch();

    if ($course) {
        echo "Latest Course Data:\n";
        print_r($course);
        
        // Check for specific issues
        if (is_null($course['title'])) echo "WARNING: Title is NULL\n";
        if (is_null($course['slug'])) echo "WARNING: Slug is NULL\n";
        if (is_null($course['price'])) echo "WARNING: Price is NULL\n";
        
        echo "Thumbnail: " . var_export($course['thumbnail'], true) . "\n";
    } else {
        echo "No courses found.\n";
    }

} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
