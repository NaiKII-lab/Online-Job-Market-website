<?php
include 'db_server.php';

$sql = "SELECT DISTINCT skills FROM user_profile_details WHERE skills IS NOT NULL";
$res = mysqli_query($conn, $sql);

$uniqueSkills = [];

while($row = mysqli_fetch_assoc($res)) {
    $skills = json_decode($row['skills'], true);
    if (is_array($skills)) {
        foreach ($skills as $s) {
            $uniqueSkills[$s] = true;
        }
    }
}

foreach ($uniqueSkills as $skill => $v) {
    echo "<button class='btn btn-outline-secondary skill-btn' data-skill='$skill'>$skill</button>";
}
?>
