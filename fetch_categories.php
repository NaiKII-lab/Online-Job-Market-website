<?php
include "db_server.php";

$mode = $_GET['mode'] ?? 'home';
$btnClass = ($mode === 'modal') ? 'modal-category-btn' : 'home-category-btn';

$sql = "SELECT DISTINCT business_type FROM jobs WHERE business_type <> '' ORDER BY business_type ASC";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<span class='text-muted'>❌ ไม่พบหมวดหมู่งาน</span>";
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $cat = htmlspecialchars($row['business_type']);
    echo "<button class='category-btn jobcat-btn $btnClass' data-cat='$cat'>$cat</button>";
}
?>
