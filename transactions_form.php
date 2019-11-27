<?php
/***
Page name : Transactions Form
Previous Page: Admin Log-In
Description : 
Database Table: 
*/
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){

	if(isset($_SESSION["admin_user_id"])){

		if((time() - $_SESSION['last_login_timestamp']) > 600){

			header("location:admin_logout.php");

		}else{

			$_SESSION['last_login_timestamp'] = time();

			require("admin_connection.php");


			$investment_amount_error = $success_message = $message = "";
			$data = array();

			if(isset($_POST["btn_login_details"])){


				if(!empty($_POST["sr_admin_id"]) && !empty($_POST["investor_id"]) && !empty($_POST["scheme_id"]) && !empty($_POST["investor_type"]) && !empty($_POST["investor_name"]) &&
					!empty($_POST["total_investment"]) && !empty($_POST["payout_date"]) &&
					!empty($_POST["payment_amount"]) && !empty($_POST["payment_ref_number"]) &&
					!empty($_POST["bank_name"]) && !empty($_POST["payment_type"]) &&
					!empty($_POST["investment_amount"])){


			    $sr_admin_id = mysqli_real_escape_string($conn,$_POST["sr_admin_id"]);
				$investor_id = mysqli_real_escape_string($conn,$_POST["investor_id"]);
				$scheme_id = mysqli_real_escape_string($conn,$_POST["scheme_id"]);
				$investor_type = mysqli_real_escape_string($conn,$_POST["investor_type"]);
				$investor_name = mysqli_real_escape_string($conn,$_POST["investor_name"]);
				$date_of_investment= date("Y-m-d",strtotime($_POST["date_of_investment"]));
				$total_investment = mysqli_real_escape_string($conn,$_POST["total_investment"]);
				$payout_date = date("Y-m-d",strtotime($_POST["payout_date"]));

                //Payment Amount 
				$pa = $_POST["payment_amount"];
				$payment_amount = implode(",",$pa);


                //Payment reference Number
				$prn = $_POST["payment_ref_number"];
				$payment_reference = implode(",",$prn);

                 //Bank Name Reference
				$bn = $_POST["bank_name"];
				$bank_name = implode(",",$bn);

                //Payment Type Reference
				$pt = $_POST["payment_type"];
				$payment_mode = implode(",",$pt);

				$required_certificate = mysqli_real_escape_string($conn,$_POST["required_certificate"]);
				$transaction_date = date("Y-m-d");

				$ia = $_POST["investment_amount"];
				$amount_on_certificate = implode(",",$ia);


                 //For Certificate generate
				$barcode_id = mysqli_real_escape_string($conn,$_POST["location_name"]);
				if((!empty($barcode_id))){
					$f1 = "SELECT * FROM barcode_details Where location_id='".$barcode_id."'";
					$result = mysqli_query($conn, $f1); 
					while($row = mysqli_fetch_assoc($result)) {
						$barcode = $row["barcode_number"];
					}



                      // Auto generate certificate Number for Investors.
					if(!empty($required_certificate)){
						$running_number  = 1;
						$f2 = "SELECT * FROM investment_transactions ORDER BY running_number DESC LIMIT 1";
						$result_2 = mysqli_query($conn, $f2); 
						$row_2 = mysqli_fetch_assoc($result_2);


						for ($i=0; $i<$required_certificate; $i++) { 
							$running_number = $row_2['running_number'] + $i;
							$certificate_number = $barcode.''.$running_number;
							$data[] = $certificate_number;
							$running_number++;
						}
					}

                     //Certificate Process Completed
					$certificate_complete = implode(",",$data);
					$q1 = "INSERT INTO investment_transactions (sr_admin_id,investor_id,scheme_id, barcode_id, running_number,certificate_number, investor_type, investor_name, date_of_investment, payout_date,payment_amount,payment_ref_number,bank_name,mode_of_payment,amount_on_certificate,total_investment,transaction_date)VALUES('$sr_admin_id','$investor_id','$scheme_id','$barcode_id','$running_number','$certificate_complete','$investor_type','$investor_name','$date_of_investment','$payout_date','$payment_amount','$payment_reference','$bank_name','$payment_mode','$amount_on_certificate','$total_investment','$transaction_date')";

					$insert = mysqli_query($conn, $q1);

					if($insert){		
						$success_message = "Transaction has been Successfully" ;
					} else {
						echo "Error:" . mysqli_error($conn);
					}
				}
			}else{
				$message = "Please fill all the Fields";
			}
		}

		include ("admin_header.php");
		include ("admin_nav.php");
		?>
		<script src="./js/ajax_jquery.min.js"></script>

		<style>

			.has-error
			{
				border-color:#cc0000;
				background-color:#ffff99;
			}
			body,h2,h3,h4,h5,h6,p,small,i,a,label,div{
				font-family:sans-serif;

			}
			.tf_1{
				background-color:#1985e2; padding:16px; color:#ffffff; margin-bottom: 20px;
			}
			@media(min-width:1024px){
				.label_size{
					font-size:11px;
				}
			}
			.mt-40{
				margin-top: 40px;
			}

			#register_form{
				border: 1px solid #e8e8e8; 
				padding:3px;
			}
			.p-10{
				padding:10px;
			}
			.tf-1{
				border:1px solid #cee0f1; border-radius: 5px; padding: 3px;
			}

		</style>

		<!--  Main page Content -->
		<div class="right_col" role="main">
			<!-- top tiles -->
			<div class="row tile_count">

				<a href="transactions.php" class="pull-left btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
				<div class="col-md-8 mt-40" >
					<?php 
					if(!empty($message)){ 
						echo '<div class="alert alert-warning alert-dismissible" role="alert" style="padding: 8px;
						margin-bottom: 20px;
						border: 1px solid #efefef00;
						border-radius: 8px;">
						<strong>'.$message.'</strong>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: relative;
						top: -2px;
						right: -2px;
						color: black;">
						<span aria-hidden="true">&times;</span>
						</button>
						</div>';
					}
					if(!empty($success_message)){ 
						echo '<div class="alert alert-success alert-dismissible" role="alert" style="padding: 8px;
						margin-bottom: 20px;
						border: 1px solid #efefef00;
						border-radius: 8px;">
						<strong>'.$success_message.'</strong>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: relative;
						top: -2px;
						right: -2px;
						color: black;">
						<span aria-hidden="true">&times;</span>
						</button>
						</div>';
					}
					if(isset($data) && !empty($data)){
						echo "Certificates Numbers:---->".'</br>';
						for ($i=0; $i < count($data) ; $i++) { 
							echo $data[$i].'</br>';
						}
					}
					?>

					<div class="col-md-12 tf_1">
						<h3 align="center"> TRANSACTION DETAILS </h3>
					</div>

					<form action="" method="post" id="register_form" >

						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="">
									<?php 
									if(isset($_GET['investor_name'])){
										echo"Investor Name : ".ucfirst($_GET['investor_name']); 
									}
									?>
								</label>
								<input type="hidden" name="investor_name" value="<?php echo $_GET['investor_name']; ?>">
								<input type="hidden" name="investor_type" value="<?php echo $_GET['investor_type'];?>">
								<input type="hidden" name="sr_admin_id" value="<?php echo $_GET['sr_admin_id'];?>">
								<input type="hidden" name="investor_id" value="<?php echo $_GET['investor_id']; ?>"> 
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-4">
								<label for="inputEmail4">Office Location</label>
								<select name="location_name" id="location_name" class="form-control">
									<option value="">Choose your City</option>
									<?php
									require("admin_connection.php");

									$f1 = "SELECT * FROM location";
									$result = mysqli_query($conn, $f1); 

									if (mysqli_num_rows($result) > 0) {

										while($row = mysqli_fetch_assoc($result)) {
											echo '<option value="'.trim($row['location_id']).'">'.$row['location_name'].'</option>';
										}
									} 
									?>
								</select>
							</div>

							<div class="form-group col-md-4">
								<label for="inputEmail4">Choose Our Plan</label>

								<select name="scheme_id" id="scheme_name" class="form-control">
									<option value="">Choose a Plan</option>
									<?php
									require("admin_connection.php");
									$f1 = "SELECT * FROM investment_scheme";
									$result = mysqli_query($conn, $f1); 

									if (mysqli_num_rows($result) > 0) {

										while($row = mysqli_fetch_assoc($result)) {

											echo '<option value="'.trim($row['scheme_id']).'">'.$row['scheme_name'].'</option>';
										}
									} 
									?>
								</select>
							</div>

							<div class="form-group col-md-4">
								<label for="inputPassword4">Date of Investment</label>
								<input type="text" name="date_of_investment" id="date_of_investment" class="form-control" readonly value="<?php echo date("d-m-Y"); ?>">
							</div>
						</div>

						<!-- Automatically Comes -->
						<div class="form-row" id="invest">
						</div>

						<!-- Automatically Comes -->
						<div class="form-row p-10" >
							<fieldset class="tf-1">
								<div class="form-group col-md-12">
									<label><h5>Payment Method <small>(For Audit Purpose) </small>:</h5></label> 
									<button class="add_field_button fa fa-plus btn btn-sm btn-primary" > Add</button>
								</div>

								<!-- Add dynamic Payment Reference Field --->
								<div class="input_fields_wrap">	</div>

							</fieldset>
						</div>

						<style>


						</style>

						<div class="form-row p-10" style="padding:10px;">
							<fieldset class="tf-1">

								<div class="form-group col-md-12">
									<label> <h5>Certificates<small> ( Maximum Certificates can be generate) 

									</small>: <b><em id="no_of_certificate"></em></b>
								</h5>
							</label> 
						</div>


						<div class="form-group col-md-12">
							<label> 
								Required Certificates:
							</label> 
							<input type="text" name="required_certificate" id="required_certificate" class="form-control" maxlength="2">
						</div>


						<!-- Add dynamic Certificate Field --->
						<div class="certificate_div">
						</div> 
					</fieldset>
				</div>



				<div class="form-row">
					<div class="form-group">
						<label class="p-10">Certificate Status : <span id="certificate_amount"></span></label> 
					</div>
				</div>


				<div class="form-row" align="right">
					<input type="submit" name="btn_login_details" id="btn_login_details" class="btn btn-primary btn-md" value="submit">
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	document.addEventListener('contextmenu', event => event.preventDefault());
</script>

<script>
	
	$(document).ready(function(){


		/************** Starting of Investment Scheme (Dropdown)- AJAX ***********************/
		$("#scheme_name").change(function(){
			var scheme_id = $(this).val();
			$.ajax({
				url:"load_scheme.php",
				method:"POST",
				data:{scheme_id:scheme_id},
				success:function(data){
					$('#invest').html(data);
				}
			});
		});
		/*************** Ending of Investment Scheme ***************************************/


		/*************** Starting of Payment Method (AutoFill Input Field)*******************/
var max_fields = 20; //maximum input boxes allowed
var wrapper  = $(".input_fields_wrap"); //Fields wrapper
var add_button = $(".add_field_button"); //Add button class

var x = 1; //initlal text box count
$(add_button).click(function(e){ //on add input button click
	e.preventDefault();
if(x < max_fields){ //max input box allowed
x++; //text box increment
$(wrapper).append('<div class="form-row"><div class="form-group col-md-2"><label class="label_size">Amount</label><input type="text" name="payment_amount[]" id="payment_amount" class="form-control payment_amount" maxlength="7" placeholder="5000000"/></div><div class="form-group col-md-3"><label class="label_size">Payment Ref. No.</label><input type="text" name="payment_ref_number[]" id="payment_ref_number" placeholder="32545224566" class="form-control payment_ref_number" maxlength="24"></div><div class="form-group col-md-3"><label class="label_size">Bank Name</label><input type="text" name="bank_name[]" id="bank_name" class="form-control  bank_name" maxlength="50" placeholder="BOI"></div><div class="form-group col-md-3"><label class="label_size">Mode of Payment</label><select name="payment_type[]" id="payment_type" class="form-control payment_type"><option value="">Select an Option</option><option value="CHEQUE">Cheque</option><option value="DD">Demand Draft</option><option value="OT">Online Transfer</option></select></div><a href="#" class="remove_field col-md-1"><i class="fa fa-close" style="color:#ffb3b3; margin-top: 30px; font-size:20px;"> </i></a></div>');
}
});

$(wrapper).on("click",".remove_field", function(e){ 
//user click on to remove text Field Area
e.preventDefault(); $(this).parent('div').remove(); x--;
});

/*************** Ending of Payment Method ******************************************/








/************************* Total Investment Amount ( Calculate ) *******************/
$('.input_fields_wrap').on('input','.payment_amount',function(e){
	e.preventDefault();
	var totalsum = 0;
	$('.form-group .payment_amount').each(function(){
		var i = $(this).val();
		if($.isNumeric(i)){
			totalsum += parseFloat(i);
		}
	});
	$('#total_investment').val(totalsum);
	var min_investment_amount =$('#min_investment_amount').val();
	if(totalsum%min_investment_amount == 0){
		no_of_certificate = totalsum/min_investment_amount;
//alert(no_of_certificate);
$('#no_of_certificate').html(no_of_certificate);
}
});
/************************ Ending of Total Investment ******************************/



/*************** Certificates Amount Arena ( Calculate ) **************************/
var required_certificates_error="";
$('.form-group').on('input','#required_certificate',function(e){
	e.preventDefault();
	var getvalue = $(this).val();
	var no_of_certificate = parseInt($("#no_of_certificate").text());

	if(getvalue <= no_of_certificate){
		var pay = '';
		if(getvalue > 0){
			for(i = 1; i <= getvalue; i++){

				pay += '<div class="form-row"><div class="form-group col-md-12"><label>Amount on Certificate</label><input  type="text" name="investment_amount[]" id="investment_amount" class="form-control investment_amount" maxlength="7" placeholder="Amount"/></div>';
				$('.certificate_div').html(pay);
			}
		}
		$("#required_certificate").removeClass("has-error");
		required_certificates_error="";
	}else{
		pay ='';
		$('.certificate_div').html(pay);
		$("#required_certificate").addClass("has-error");
		required_certificates_error="yes";
	}
});

/***************************Ending of Certificate Amount Arena********************/


/************************ Certificate Investment Amount (Printed on Certificates)**************************/
$('.certificate_div').on('input','.investment_amount',function(e){
	e.preventDefault();
	var total_sum = 0;

	$('.certificate_div .investment_amount').each(function(){
		var i = $(this).val();
		if($.isNumeric(i)){
			total_sum += parseFloat(i);
		}
	});
	var total_i = $('#total_investment').val();
	if(total_sum == total_i){
		$('#certificate_amount').html('<i style="color:green">SUCCESS</i>');
	}else{
		$('#certificate_amount').html('<i style="color:red"> NOT SUCCESS</i>');
	}
});
/************ Ending of Certificate Investment Amount****************************/


$('#btn_login_details').submit(function(){

//Field Validation
var payment_type = scheme_name = payment_ref_number = bank_name = location_name = "";

var payment_type_error = scheme_name_error = payment_ref_number_error = bank_name_error = location_name_error = investment_amount_error=no_of_certificate_error=  required_certificate_error = "";

//Location Field Validation 
var location_name = $('#location_name');
if(location_name.val() == ""){
	$('#location_name').addClass('has-error');
	location_name_error == "yes";
}else{
	$('#location_name').removeClass('has-error');
	location_name_error == "";
}


//Scheme Field Validation 
var scheme_name = $('#scheme_name');
if(scheme_name.val() == ""){
	$('#scheme_name').addClass('has-error');
	scheme_name_error == "yes";
}else{
	$('#scheme_name').removeClass('has-error');
	scheme_name_error == "";
}

//Check Number of Certificates
var aa_filter =  /^\d$/g;
if($.trim($('#no_of_certificate').val()).length == 0){
	$('#no_of_certificate').addClass('has-error');
	no_of_certificate_error == "yes";

}else{
	if(!aa_filter.test($('#no_of_certificate').val())){
		$('#no_of_certificate').addClass('has-error');
		no_of_certificate_error == "yes";

	}else{
		no_of_certificate_error == "";
		$('#no_of_certificate').removeClass('has-error');
	}
}

/************** Certificates Field Verification *************/
var investment_amount_error = '';
var ia_filter = /^[0-9]+$/;
$('.investment_amount').each(function(index, value) {
	var a = $(value).val();
	if( a.length == 0){
		$(value).addClass('has-error');
		investment_amount_error = "yes"; 
	}else{
		if((!ia_filter.test($(value).val()))){
			$(value).addClass('has-error');
			investment_amount_error = "yes";
		}else{
			$(value).removeClass('has-error');
			investment_amount_error = "";
		}
	}
});
/************** Ending of Certificates Field Verification ******/



/******************* Payment Method Field Verification **************/
var required_certificate = $('#required_certificate');
if(required_certificate.val() == ""){
	$('#required_certificate').addClass('has-error');
	required_certificate_error == "yes";
}else{
	$('#required_certificate').removeClass('has-error');
	required_certificate_error == "";
}

//Amount Field Validation
var payment_amount_error = '';
var a_filter = /^[0-9]+$/;
$('.payment_amount').each(function(index, value) {
	var a = $(value).val();
	if( a.length == 0){

		$(value).addClass('has-error');
		payment_amount_error = "ews";
	}else{

		if(!a_filter.test($(value).val())){
			$(value).addClass('has-error');
			payment_amount_error = "tes";
		}else{
			$(value).removeClass('has-error');
			payment_amount_error = "";
		}
	}
});

//Payment Reference Number Field Validation
var payment_ref_error = '';
var pr_filter = /^[0-9a-zA-Z]+$/;
$('.payment_ref_number').each(function(index, value) {
	var a = $(value).val();
	if( a.length == 0){

		$(value).addClass('has-error');
		payment_ref_error = "ews";
	}else{

		if(!pr_filter.test($(value).val())){
			$(value).addClass('has-error');
			payment_ref_error = "tes";
		}else{
			$(value).removeClass('has-error');
			payment_ref_error = "";
		}
	}
});

//Bank Name Field Validation
var bank_name_error = '';
var bn_filter = /^[0-9a-zA-Z ]+$/;
$('.bank_name').each(function(index, value) {
	var a = $(value).val();
	if( a.length == 0){

		$(value).addClass('has-error');
		bank_name_error = "ews";
	}else{

		if(!bn_filter.test($(value).val())){
			$(value).addClass('has-error');
			bank_name_error = "tes";
		}else{
			$(value).removeClass('has-error');
			bank_name_error = "";
		}
	}
});

//Bank Name Field Validation
var payment_type_error = '';
var bn_filter = /^[0-9a-zA-Z ]+$/;
$('.payment_type').each(function(index, value) {
	var a = $(value).val();
	if(a == ""){
		$(value).addClass('has-error');
		payment_type_error = "sdf";
	}else{
		$(value).removeClass('has-error');
		payment_type_error = "";
	}
});
/************************************************************/
if(payment_amount_error !="" ||payment_type_error !=''|| scheme_name_error !=''|| payment_ref_error !=''|| bank_name_error !='' || location_name_error != ''||investment_amount_error!=''||no_of_certificate_error!=''||investment_amount_error!='')
{
	return false;
}
else
{
	$("#register_form").trigger('submit');
//return true;
}
});
});
</script>
<?php 
include("admin_footer.php"); 
}}}
?>




