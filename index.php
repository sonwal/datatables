<?php
/**
 * Useful links:
 * https://datatables.net/manual/server-side
 *
 */

if (!empty($_POST['validRequest']) && !empty($_POST["draw"])) {
	
	/*
	echo "<pre>";
		print_r($_POST);
		exit;*/
	
	// normal array data case.
	/*
	$arr[] = array(
			"name" => "Manish Sonwal",
			"position" => "1st",
			"office" => "Shopclues",
			"extn" => "SE",
			"start" => "5",
			"salary" => "6",
		);
		
		$arr[] = array(
			"name" => "Anonymous User",
			"position" => "2nd",
			"office" => "Un named company",
			"extn" => "no position",
			"start" => "1",
			"salary" => "2",
		);
		
		$filteredArr = $arr;
		// searching on columns.
		if(!empty($_POST["search"]['value'])){
			foreach ($arr as $key => $value) {
				if(empty(preg_grep("/^(".$_POST["search"]['value'].")/i", $value)))
					unset($filteredArr[$key]);
			}
			$filteredArr = array_values($filteredArr);
		}
		
		
		$mainArr = array(
			"draw" => (int)$_POST["draw"],
			"recordsTotal" => count($arr),
			"recordsFiltered" => 10,
			"data" => $filteredArr
		);*/
	
	// mysql data case.
	$link = mysqli_connect("localhost", "root", "root", "test");
	$where = "";
	$orderBy = "";
	$start = $_POST["start"];
	$limit = $_POST["length"];
	$resArr= array();
	
	// searching on columns.
	if(!empty($_POST["search"]['value'])){
		$where = " where ";
		$where .= " cpd.name like '".$_POST["search"]['value']."%' or ";
		$where .= " cp.coupon_code like '".$_POST["search"]['value']."%' or ";
		$where .= " cp.zone like '".$_POST["search"]['value']."%' or ";
		$where .= " cpd.lang_code like '".$_POST["search"]['value']."%' or ";
		$where .= " cp.priority like '".$_POST["search"]['value']."%' or ";
		$where .= " cp.number_of_usages like '".$_POST["search"]['value']."%'";
	}
	
	// order by 
	if(!empty($_POST["order"][0]['dir'])){
		$orderBy = " order by ".($_POST["order"][0]['column']+1)." ".$_POST["order"][0]['dir'];
	}
	
	// pagination.
	
	
	$totalSql = "select count(*) as total_val from cscart_promotions cp inner join cscart_promotion_descriptions cpd on cpd.promotion_id=cp.promotion_id";
	$resTot = mysqli_query($link, $totalSql);
	$resultTot = mysqli_fetch_assoc($resTot);
	
	$sql = "select cpd.name,cp.coupon_code as position,cp.zone as office,cpd.lang_code as extn,cp.priority as start,cp.number_of_usages as salary from cscart_promotions cp inner join cscart_promotion_descriptions cpd on cpd.promotion_id=cp.promotion_id ".$where." ".$orderBy." limit ".$start.",".$limit;
	
	$res = mysqli_query($link, $sql);
	
	if(!empty($res))
		while($result = mysqli_fetch_assoc($res))
			$resArr[] = $result;
	
	$mainArr = array(
		"draw" => (int)$_POST["draw"],
		"recordsTotal" => $resultTot['total_val'],
		"recordsFiltered" => $resultTot['total_val'],
		"data" => $resArr
	);
	die(json_encode($mainArr));
}
?>
<script src="media/js/jquery.js"></script>
<script src="media/js/jquery.dataTables.min.js"></script>
<script src="media/js/dataTables.jqueryui.min.js"></script>
<link rel="stylesheet" type="text/css" href="media/css/jquery.dataTables.min.css">
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Name</th>
			<th>Position</th>
			<th>Office</th>
			<th>Extn.</th>
			<th>Start date</th>
			<th>Salary</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th>Name</th>
			<th>Position</th>
			<th>Office</th>
			<th>Extn.</th>
			<th>Start date</th>
			<th>Salary</th>
		</tr>
	</tfoot>
</table>
<script>
	$(document).ready(function() {
		$('#example').DataTable({
			"processing" : true,
			"serverSide" : true,
			"ajax" : {
				"url" : "http://localhost/datatables/index.php",
				"type" : "POST",
				"data" : {
					"validRequest" : true	// for making post request valid, a kind of key.
				}
			},
			"columns" : [
				{"data" : "name"},
				{"data" : "position"},
				{"data" : "office"},
				{"data" : "extn"},
				{"data" : "start"},
				{"data" : "salary"}
			]
		});
	}); 
</script>