<?php
$sql = "SELECT * FROM rooms WHERE available_from <= CURDATE() AND available_to >= CURDATE()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Room Type: " . $row["room_type"] . " - Price: " . $row["room_price"] . "<br>";
    }
} else {
    echo "No rooms available.";
}
?>
