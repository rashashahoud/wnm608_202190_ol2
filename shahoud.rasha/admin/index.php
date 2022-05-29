<?php


include "../lib/php/functions.php";
 
$empty_product = (object)[
	"name"=>"Pen",
	"price"=>"4.99",
	"description"=>"I only about it from Zelda",
	"dimension"=>"4",
	"color"=>"Gold",
	"thumbnail"=>"gold.jpeg",
	"images"=>"gold.jpeg",
	"quantity"=>"20"
];


//LOGIC
if(isset($_GET['action'])){
try {
	$conn = makePDOConn();
	switch($_GET['action']) {
		case "update":
		$statement = $conn->prepare("UPDATE
			`products`
			SET
				`name`=?,
				`price`=?,
				`quantity`=?,
				`description`=?,
				`dimension`=?,
				`color`=?,
				`thumbnail`=?,
				`images`=?,
				`date_modify`=NOW()
			WHERE `id`=?
			");
		$statement->execute([
			$_POST['product-name'],
			$_POST['product-price'],
			$_POST['product-quantity'],
			$_POST['product-description'],
			$_POST['product-dimension'],
			$_POST['product-color'],
			$_POST['product-thumbnail'],
			$_POST['product-images'],
			$_GET['id']
		]);
		header("location:{$_SERVER['PHP_SELF']}?id={$_GET['id']}");
		break;

		case"create":	
		$statement = $conn->prepare("INSERT INTO
			`products`
			(
				`name`,
				`price`,
				`quantity`,
				`description`,
				`dimension`,
				`color`,
				`thumbnail`,
				`images`,
				`date_create`,
				`date_modify`
			)
			VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())
			");
		$statement->execute([
			$_POST['product-name'],
			$_POST['product-price'],
			$_POST['product-quantity'],
			$_POST['product-description'],
			$_POST['product-dimension'],
			$_POST['product-color'],
			$_POST['product-thumbnail'],
			$_POST['product-images']
		]);
		$id = $conn->lastInsertId();
		header("location:{$_SERVER['PHP_SELF']}?id=$id");
		break;

		case "delete":	
		$statement = $conn->prepare("DELETE FROM `products` WHERE id=?");
		$statement->execute([$_GET['id']]);
		header("location:{$_SERVER['PHP_SELF']}");
		break;
	}
} catch(PDOException $e){
	die($e->getMessage());
}
}





// TEMPLATES


function productListItem($r,$o){

return $r.<<<HTML
<div class="card soft">
	<div class="display-flex">
	<div class="flex-none images-thumbs"><img src='img/$o->thumbnail'></div>
	<div class="flex-stretch" style="padding: 1em;">$o->name</div>
	<div class="flex-none"><a href="{$_SERVER['PHP_SELF']}?id=$o->id" class="form-button">Edit</a></div>
	</div>
</div>
HTML;
}




function showProductPage($o){

	$id = $_GET['id'];
	$addoredit = $id == "new" ? "Add" : "Edit";
	$createorupdate = $id == "new" ? "create" : "update";
	$images = array_reduce(explode(",",$o->images),function($r,$o){return $r."<img src='img/$o'>";});


$display =  <<<HTML
<div>
<h2>$o->name</h2>
<div class="form-control">
<label class="form-label">Price</label>
<span>&dollar;$o->price</span>
</div><div class="form-control">
<label class="form-label">quantity</label>
<span>$o->quantity</span>
</div>
<div class="form-control">
<label class="form-label">Description</label>
<span>$o->description</span>
</div>
<div class="form-control">
<label class="form-label">Dimension</label>
<span>$o->dimension</span>
</div>
<div class="form-control">
<label class="form-label">Color</label>
<span>$o->color</span>
</div>
<div class="form-control">
<label class="form-label">Thumbnail</label>
<span class='images-thumbs'><img src = 'img/$o->thumbnail'></span>
</div>
<div class="form-control">
<label class="form-label">Other Images</label>
<span class='images-thumbs'>$images</span>
</div>
</div>
HTML;
 

$form = <<<HTML

	<div class="container">
	<div class="display-flex flex-align-center">
	<div class="flex-stretch">
	<h2>Product Editor Form</h2>
	<div class="container" id="forms">
	<div class="card soft">
	<form method="post" action="{$_SERVER['PHP_SELF']}?id=$id&action=$createorupdate">
	<h2>$addoredit Product</h2>


	<div class="form-control">
	<label class="form-label" for="product-name">Product</label>
	<input type="text" name="product-name" id="product-name" value="$o->name" class="form-input" placeholder="Enter The Product Name">
	</div>

	<div class="form-control">
	<label class="form-label" for="product-price">Price</label>
	<input type="Number" name="product-price" id="product-price" min="0" max="1000" step="0.01" value="$o->price" class="form-input" placeholder="Enter The Product Price">
	</div>

	<div class="form-control">
	<label class="form-label" for="product-quantity">Quantity</label>
	<input type="Number" name="product-quantity" id="product-quantity" min="0" max="1000" step="1" value="$o->quantity" class="form-input" placeholder="Enter The Product Quantity">
	</div>

	<div class="form-control">
	<label class="form-label" for="product-description">Description</label>
	<textarea type="text" name="product-description" id="product-description" class="form-input" placeholder="Enter The Product Description" style="height: 6em;">$o->description</textarea>
	</div>

	<div class="form-control">
	<label class="form-label" for="productdimensionl">dimension</label>
	<input type="text" name="product-dimension" id="product-dimension" value="$o->dimension" class="form-input" placeholder="Enter The Product dimension">
	</div>


	<div class="form-control">
	<label class="form-label" for="product-color">Color</label>
	<input type="text" name="product-color" id="product-color" value="$o->color" class="form-input" placeholder="Enter The Product Color">
	</div>

	<div class="form-control">
	<label class="form-label" for="product-thumbnail">Thumbnail</label>
	<input type="text" name="product-thumbnail" id="product-thumbnail" value="$o->thumbnail" class="form-input" placeholder="Enter The Product Thumbnail">
	</div>


	<div class="form-control">
	<label class="form-label" for="product-images"> Other Images </label>
	<input type="text" name="product-images" id="product-images" value="$o->images" class="form-input" placeholder="Enter The Product Images">
	</div>


	<div class="form-control">
	<input type="submit" class="form-button" value="Save Changes">
	</div>



	</div>
	</form>


	</div>
	</div>
	</div>


HTML;

	$output = $id == "new" ?"<div class='card soft'> $form</div>" :
	"<div class='grid gap'>
	<div class='col=xs-12 col-md-7'><div class='card soft'> $display</div></div>
	<div class='col=xs-12 col-md-5'><div class='card soft'> $form</div></div>
	</div>
	";

	$delete = $id == "new" ? "" : "<a href='{$_SERVER['PHP_SELF']}?id=$id&action=delete'>Delete</a>";
	echo <<<HTML
	<div class='card soft'>
		<nav class="display-flex">
			<div class="flex-stretch"><a href="{$_SERVER['PHP_SELF']}"> ⏎ BACK </a></div>
			<div class="flex-none"> $delete</a></div>
	
		</nav>
	</div>
	$output
	HTML;
}



?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	
	<?php include "../parts/meta.php"; ?>


	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>
	
<header class="navbar">
	<div class="container display-flex">
		<div class="flex-none">
			<h1>Product Admin Page</h1>
			<nav class="nav nav-flex flex-none">
				<ul>
					<li><a href="<?=$_SERVER['PHP_SELF']?>">Product List</a></li>
					<li><a href="<?=$_SERVER['PHP_SELF']?>?id=new">Add New Product</a></li>

				</ul>
			</nav>
		</div>
	</div>
</header>

<div class="container">
	<?php

			if(isset($_GET['id']))  {
			showProductPage(
				$_GET['id']=="new"?
					$empty_product : 
					makeQuery(makeConn(), "SELECT * FROM `products` WHERE `id` = ".$_GET['id'])[0]
			);
			} else {


				?>


				<h2 class="ulist">Product List</h2>

				<nav class="nav">
					<ul class="product">
						

						<?php

						$result = makeQuery(makeConn(), "SELECT * FROM `products` ORDER BY `date_create` DESC");

						echo array_reduce($result,'productListItem');

					}

					
					?>
						
						<php } ?>
					</ul>
				</nav>


</div>

</body>
</html>_