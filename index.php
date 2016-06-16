<html>
<header>
<title>Price Watcher</title>
</header>
<body>

<?php
// load credentials file, connect to db, and catch connection errors
include("/var/www/lib/cred.php");
$conn = mysqli_connect('localhost', $user, $password, 'pricewatch');
if(! $conn ) {
  die( 'Could not connect: ' . mysqli_connect_error() );
}

// show whether adding an entry was successful or not
if( isset($_POST['add']) ) {
	$item_link = rawurlencode( $_POST['item_link']);
	if ( $stmt = mysqli_prepare($conn, "INSERT INTO items (link) VALUES (?)") ) {
		mysqli_stmt_bind_param($stmt, "s", $item_link);
		mysqli_stmt_execute($stmt);
		if( mysqli_stmt_affected_rows($stmt) != 1 ) {
	  		die( 'Could not enter data: ' . mysqli_error($conn) );
		}
		mysqli_stmt_close($stmt);
		echo "Entered data successfully <br />";
		echo "<a href='index.php'>Back to Main Page</a>";
	}

// show whether deleting an entry was successful or not
} else if( isset($_POST['delete']) ) {
	$id = $_POST['itemid'];
	$sql = "DELETE FROM items WHERE id='$id'";
	$retval = mysqli_query($conn, $sql);
	if(! $retval ) {
  		die( 'Could not delete data: ' . mysqli_error($conn) );
	}
	echo "Deleted data successfully<br />";
	echo "<a href='index.php'>Back to Main Page</a>";

// main page: load item links from table and scrape prices from these sites
} else {
	?>	
	<h1>Price Watcher</h1>
	<table>
	<tr>
		<td><h3>Product</h3></td>
		<td><h3>Current Price</h3></td>
		<td><h3>Item Link</h3></td>
	</tr>
	<?php
	$sql = "SELECT link, id FROM items";
	$result = mysqli_query($conn, $sql);
	while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
		$displayurl = rawurldecode($row['link']);
		$html = file_get_contents($displayurl); 
		$regex1 = '/<span id="priceblock_ourprice" class="a-size-medium a-color-price">(.+?)<\/span>/';
	 	preg_match($regex1, $html, $price);
	 	$regex2 = '/<span id="productTitle" class="a-size-large">[\n\s]*(.+?)[\n\s]*<\/span>/';
	 	preg_match($regex2, $html, $product);
	 	echo "<tr><td>".$product[1]."</td>";
	 	echo "<td>".$price[1]."</td>";
	    echo "<td><a href='".$displayurl."'>".$displayurl."</a></td>";
	    echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>
	    		<td><input type='hidden' name='itemid' value='".$row['id']."'>
	    			<input type='submit' name='delete' value='Delete'>
	    		</td></form></tr>";
	}
    mysqli_free_result($result);
    ?>
    </table>
    <form method="post" action="<?php $_PHP_SELF ?>">
    <input name="item_link" type="text" id="item_link" size="150">
    <input name="add" type="submit" id="add" value="Add Item">
    </form>
<?php
}

mysqli_close($conn);
?>

</body>
</html>
