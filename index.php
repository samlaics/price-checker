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
	$item_link = $_POST['item_link'];
	$sql = "INSERT INTO items (link) VALUES ('$item_link')";
	$retval = mysqli_query($conn, $sql);
	if(! $retval ) {
  		die( 'Could not enter data: ' . mysqli_error($conn) );
	}
	echo "Entered data successfully <br />";
	echo "<a href='index.php'>Back to Main Page</a>";

// show whether deleting an entry was successful or not
} else if( isset($_POST['delete']) ) {
	$id=$_POST['itemid'];
	$sql = "DELETE FROM items WHERE link='$id'";
	$retval = mysqli_query($conn, $sql);
	if(! $retval ) {
  		die( 'Could not delete data: ' . mysqli_error($conn) );
	}
	echo "Deleted data successfully <br />";
	echo "<a href='index.php'>Back to Main Page</a>";

// main page: load item links from table and scrape prices from these sites
} else {
	?>
	<form method="post" action="<?php $_PHP_SELF ?>">
	<h1>Price Watcher</h1>
	<table>
	<tr>
		<td><h3>Product</h3></td>
		<td><h3>Current Price</h3></td>
		<td><h3>Item Link</h3></td>
	</tr>
	<?php
	$sql = "SELECT link FROM items";
	$result = mysqli_query($conn, $sql);
	while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
		$html = file_get_contents($row['link']); 
		$regex1 = '/<span id="priceblock_ourprice" class="a-size-medium a-color-price">(.+?)<\/span>/';
	 	preg_match($regex1, $html, $price);
	 	$regex2 = '/<span id="productTitle" class="a-size-large">[\n\s]*(.+?)[\n\s]*<\/span>/';
	 	preg_match($regex2, $html, $product);
	 	echo "<tr><td>".$product[1]."</td>";
	 	echo "<td>".$price[1]."</td>";
	    echo "<td><a href='".$row['link']."'>".$row['link']."</a></td>";
	    echo "<td><input type='hidden' name='itemid' value='".$row['link']."'><input type='submit' name='delete' value='Delete'></td></tr>";
	}
    mysqli_free_result($result);
    ?>
    </table>
    <input name="item_link" type="text" id="item_link" size="200">
    <input name="add" type="submit" id="add" value="Add Item">
    </form>
<?php
}

mysqli_close($conn);
?>

</body>
</html>
