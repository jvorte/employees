<?php
define("ROW_PER_PAGE",5);
require_once('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .wrapper{
            width: 800px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 140px;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>

</head>
<body>


 <!-- ------------------------------------ nav------------------------------------------------------------------------ -->

 

    <div class="container">
  
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Employees</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li>
      </ul>
  
    </div>
  </div>
</nav>
    </div>

    <!-- ------------------------------------end nav------------------------------------------------------------------------ -->



 <!-- ----------------------------------------------------------------------------------------------------------- -->

    
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Employees Details</h2>
                        <a href="create.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";

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
<form name='frmSearch' action='' method='post'>
<div style='text-align:right;margin:20px 0px;'><input type='text' name='search[keyword]' value="<?php echo $search_keyword; ?>" id='keyword' maxlength='25'></div>

<?php
  // Attempt select query execution
  // $sql = "SELECT * FROM employees";
  // if($result = $pdo->query($sql)){
  //     if($result->rowCount() > 0){
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

              
             
if (!empty($result)) {
    foreach ($result as $row) {
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
}
              
              echo "</tbody>";  
                                        
          echo "</table>";
          // Free result set
          unset($result);
    
 
  
  // Close connection
  unset($pdo);

 
?>
<?php echo $per_page_html; ?>
</form>


 
   
                </div>
            </div>        
        </div>
    </div>
</body>
</html>