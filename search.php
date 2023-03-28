<?php
define("ROW_PER_PAGE",2);
require_once('config.php');
?>
<html>
<head>
<style>
body{width:615px;font-family:arial;letter-spacing:1px;line-height:20px;}
.tbl-qa{width: 100%;font-size:0.9em;background-color: #f5f5f5;}
.tbl-qa th.table-header {padding: 5px;text-align: left;padding:10px;}
.tbl-qa .table-row td {padding:10px;background-color: #FDFDFD;vertical-align:top;}
.button_link {color:#FFF;text-decoration:none; background-color:#428a8e;padding:10px;}
#keyword{border: #CCC 1px solid; border-radius: 4px; padding: 7px;background:url("demo-search-icon.png") no-repeat center right 7px;}
.btn-page{margin-right:10px;padding:5px 10px; border: #CCC 1px solid; background:#FFF; border-radius:4px;cursor:pointer;}
.btn-page:hover{background:#F0F0F0;}
.btn-page.current{background:#F0F0F0;}
</style>
</head>
<body>
<?php	
	$search_keyword = '';
	if(!empty($_POST['search']['keyword'])) {
		$search_keyword = $_POST['search']['keyword'];
	}
	$sql = 'SELECT * FROM employees WHERE name LIKE :keyword OR address LIKE :keyword OR salary LIKE :keyword ORDER BY id DESC ';
	
	/* Pagination Code starts */
	$per_page_html = '';
	$page = 1;
	$start=0;
	if(!empty($_POST["page"])) {
		$page = $_POST["page"];
		$start=($page-1) * ROW_PER_PAGE;
	}
	$limit=" limit " . $start . "," . ROW_PER_PAGE;
	$pagination_statement = $pdo->prepare($sql);
	$pagination_statement->bindValue(':keyword', '%' . $search_keyword . '%', PDO::PARAM_STR);
	$pagination_statement->execute();

	$row_count = $pagination_statement->rowCount();
	if(!empty($row_count)){
		$per_page_html .= "<div style='text-align:center;margin:20px 0px;'>";
		$page_count=ceil($row_count/ROW_PER_PAGE);
		if($page_count>1) {
			for($i=1;$i<=$page_count;$i++){
				if($i==$page){
					$per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page current" />';
				} else {
					$per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page" />';
				}
			}
		}
		$per_page_html .= "</div>";
	}
	
	$query = $sql.$limit;
	$pdo_statement = $pdo->prepare($query);
	$pdo_statement->bindValue(':keyword', '%' . $search_keyword . '%', PDO::PARAM_STR);
	$pdo_statement->execute();
	$result = $pdo_statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div style='text-align:right;margin:20px 0px;'><input type='text' name='search[keyword]' value="<?php echo $search_keyword; ?>" id='keyword' maxlength='25'></div>
<div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Employees Details</h2>
                        <a href="create.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
if (!empty($result)) {
    foreach ($result as $row) {
        // Attempt select query execution
        $sql = "SELECT * FROM employees";
        if ($result = $pdo->query($sql)) {
            if ($result->rowCount() > 0) {
                echo '<table class="table table-bordered ">';
                echo "<thead>";
                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>Name</th>";
                echo "<th>Address</th>";
                echo "<th>Salary</th>";
                echo "<th>Photo</th>";
                echo "<th>Action</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                while ($row = $result->fetch()) {
                    echo "<tr class='table-row'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['address'] . "</td>";
                    echo "<td>" . $row['salary'] . " €"."</td>";
                    echo "<td>" . "<img style='width:80px;' src='uploads/".$row['image']."' >". "</td>";
                    echo "<td>";

                    echo '<a href="read.php?id='. $row['id'] .'" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                    echo '<a href="update.php?id='. $row['id'] .'" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                    echo '<a href="delete.php?id='.$row['id'].'&image='.$row['image'].'" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";

                echo "</table>";
                // Free result set
                unset($result);
            } else {
                echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}
                    
                    // Close connection
                    unset($pdo);
                 
             
                  
                    ?>
                    <?php echo $per_page_html; ?>
</body>
</html>