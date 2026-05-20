<?php
include("db_server.php");
mysqli_set_charset($conn, "utf8mb4");

$sql = "SELECT DISTINCT business_type FROM jobs WHERE business_type <> '' ORDER BY business_type";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<span class='text-muted'>❌ ไม่มีหมวดหมู่งานในระบบ</span>";
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $cat = htmlspecialchars($row['business_type'], ENT_QUOTES);
    echo "<button class='btn btn-outline-secondary btn-sm category-btn' data-cat=\"$cat\">$cat</button>";
}
