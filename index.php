<html>
<header>
<title>Price Watcher</title>
</header>
<body>
<h1>Price Watcher</h1>

<table>
<tr><td><h3>Item Link</h3></td>
	<td><h3>Current Price</h3></td>
</tr>

<?php
// load credentials file, connect to db, and catch connection errors
include("/var/www/lib/cred.php");
$conn = mysqli_connect('localhost', $user, $password, 'pricewatch');
if(! $conn ) {
  die('Could not connect: ' . mysqli_connect_error());
}

// load item links from table and scrape prices from these sites
$sql = "SELECT link, expected_price FROM items";
$result = mysqli_query($conn, $sql);
while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
	$html = file_get_contents($row['link']); 
	$regex = '/<span id="priceblock_ourprice" class="a-size-medium a-color-price">(.+?)<\/span>/';
 	preg_match($regex,$html,$price);
    echo "<tr><td><a href='".$row['link']."'>".$row['link']."</a></td>";
    echo "<td>".$price[1]."</td></tr>";
}

mysqli_free_result($result);
mysqli_close($conn);
?>

</table>
</body>
</html>
