<?php
require 'db.php';

$query = $pdo->query("SHOW TABLES");
while ($table = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "Table trouvée : " . implode(", ", $table) . "<br>";
}
?>
