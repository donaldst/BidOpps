<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Opportunity</title>

    <!-- Bootstrap core CSS -->
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="CSS/home.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css" integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Baloo|Caudex|Happy+Monkey|Karma|Lilita+One|ABeeZee|Antic|Average|Khula|Montserrat+Alternates|Nanum+Gothic|Nobile|Nunito|Varela+Round|Zilla+Slab" rel="stylesheet">
  </head>

  <body>
  
<?php
	include_once('action/connection.php');
	include_once('action/checkLogin.php');
	
	if(isset($_GET['id'])){
            
            //Fetch submission
            $submission_id = mysqli_real_escape_string($bd, $_GET['id']);
            $query = "SELECT * FROM submissions WHERE id = '".$submission_id."'";
            
            $result = mysqli_query($bd, $query);
            if(!$result) echo "Submission could not be fetched.";
            else if (mysqli_num_rows($result) == 0 ) $noResult = true;
            else{
                    $submission = mysqli_fetch_assoc($result);
            }
            mysqli_free_result($result);
            
            //Fetch Opportunity
            $opportunity_id = $submission['opportunity_id'];
            $query = "SELECT * FROM opportunities WHERE id = '".$opportunity_id."'";
            
            $result = mysqli_query($bd, $query);
            if(!$result) echo "Opportunity could not be fetched.";
            else if (mysqli_num_rows($result) == 0 ) $noResult = true;
            else{
                    $opportunity = mysqli_fetch_assoc($result);
            }
            mysqli_free_result($result);
            
            //Fetch Documents
            $query = "SELECT * FROM opportunity_docs WHERE opportunity_id = ".$opportunity_id."";
            
            $result = mysqli_query($bd, $query);
            if(!$result) echo "Documents could not be fetched.";
            else{
                $documents = mysqli_fetch_assoc($result);
            }
            mysqli_free_result($result);
            
            //Fetch Bidder
            $query = "SELECT * FROM users JOIN bidders WHERE id = ".$submission['bidder_id'];
            
            $result = mysqli_query($bd, $query);
            if(!$result) echo "Bidder could not be found.";
            else{
                $bidder = mysqli_fetch_assoc($result);
            }
            mysqli_free_result($result);
            
            //Check Permissions
            $query = "SELECT * FROM permissions WHERE user_id = ".$_SESSION['SESS_MEMBER_ID']."";
  
            $result = mysqli_query($bd, $query);
            if(!$result) echo "Permissions could not be checked.";
            else{
                $permissions = mysqli_fetch_assoc($result);
            }
            mysqli_free_result($result);
            
        }
        else{
            header("Location: home.php");
        }
	
?>
	<nav class="navbar fixed-top" style="background-color:#20a8f7;">
        <a href="home.php" style="text-decoration:none;"><h2 class="navbar-brand" style="font-size:30px;font-family:'Nunito';color:white;">Bid Opportunities</h2></a>
	 <div class="dropdown pr-5">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		   <?php echo  $_SESSION['SESS_FIRST_NAME']   ?>
		 <span class="caret"></span>
		 </button>
		 <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
		 <li><a href="action/adminLogout.php">Logout</a></li>
		 </ul>
	  </div>
	</nav> <br/>
	
	<div class="container-fluid">
		<div class="card">
                    <div class="card-header">Bid Submission Review</div>
			<div class="card-body">
                            <?php if(isset($noResult)): echo "Submission 'id = ".$submission_id."' does not exist."; else: ?>
                            <h5>Opportunity Number</h5> <?=$opportunity['number']; ?><br><hr>
                            <h5>Title</h5> <?=$opportunity['title']; ?><br><hr>
                            <h5>Category</h5> <?=$opportunity['category']; ?><br><hr>
                            <h5>Bidder Information</h5> 
                            <h6>Name:</h6><?=$bidder['firstname']." ".$bidder['lastname']; ?><br>
                            <br><h6>Business:</h6><?=$bidder['business'];?><hr>
                            <h5>Status</h5> <?=$submission['status']; ?><br>
                            <hr>
                            <h5>Bidder Uploads</h5>
                            <table id="documents" class="table table-striped table-bordered mt-2" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:50%">File Name</th>
                                            <th style="width:50%">Posted Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    // Fetches rows from the $documents mysqli result to populate table
                                    $query2 = "SELECT * FROM submission_docs WHERE submission_id = ".$submission_id."";
                                    $submision = mysqli_query($bd, $query2);
                                    if(mysqli_num_rows($submision) > 0 ):
                                    while($submisions = mysqli_fetch_assoc($submision)): ?>
									<tr>
                                    <td><a href="<?php echo $submisions['directory']; ?>"><?php echo $submisions['filename']; ?></a></td>
                                    <td><?php echo $submisions['posted_date']; ?></td>
                                    <?php endwhile; else: echo '<td colspan="2">No files found.</td>'; endif; 
                                    //End fetch rows
                                    ?>
                                    </tr>
                                    </tbody>
                                </table>
				<!--Document Display Module goes here-->
				
			</div>
			<div class="card-footer">
                            <a class="btn btn-info" href="home.php"><i class="fas fa-home"></i> Home</a>
                            <button type="button" class="btn btn-info float-right" data-toggle="modal" data-target="#commentModal"><i class="fas fa-comment"></i> View Comment</button>
				<!-- Options to display based on user and status -->
                                <?php if($submission['status'] != 'Awarded' && $submission['status'] != 'Denied' && ($permissions['administrate'] || $permissions['screen'] || $permissions['evaluate'] || $permissions['finalize'])): ?>
				<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal"><i class="fas fa-ban"></i> Reject</button>
                                <?php endif; if($submission['status'] == 'Submitted' && ($permissions['administrate'] || $permissions['screen'])): ?>
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#screenModal"><i class="far fa-paper-plane"></i> Screen</button>
                                <?php elseif($submission['status'] == 'Screened' && ($permissions['administrate'] || $permissions['evaluate'])): ?>
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#evaluateModal"><i class="fas fa-clipboard-check"></i> Evaluate</button>
				<?php elseif($submission['status'] == 'Evaluated' && ($permissions['administrate']|| $permisssions['finalize'])): ?>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#awardModal"><i class="fas fa-trophy"></i> Award this Bid</button>
                                <?php endif; ?>
			</div>
                    <?php endif; ?>
		</div>
		
		<!-- Cancel Submission Modal -->
		<div id="cancelModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center">Are you sure you want to reject this submission?</h4>
				</div>
				<div class="modal-body">
                                    <h6>This submission will be removed from the submission process and archived.</h6>
                                        <textarea class="input-block-level" name="remove-comment" id="remove-comment"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i class="far fa-window-close"></i> Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="remove" value="remove"><i class="fas fa-ban"></i> Reject</button>
				</div>
			</div>
		</div>
		</div>
		
		<!--Screener Modal-->
		<div id="screenModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Review Submission</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<textarea class="input-block-level" name="screen-comment" id="screen-comment" required></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i class="far fa-window-close"></i> Cancel</button>
					<button type="submit" class="btn btn-success" name="screen" value="screen"><i class="far fa-paper-plane"></i> Submit for Approval</button>
				</div>
			</div>
		</div>
		</div>
		
		<!-- Evaluator Modal -->
		<div id="evaluateModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Evaluate Submission</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
                                    <label for="score">Score</label>
                                    <input class="form-control" type="number" name="score" id="score">
                                    <textarea class="input-block-level" name="evaluate-comment" id="evaluate-comment"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i class="far fa-window-close"></i> Cancel</button>
                                        <button type="submit" class="btn btn-success" name="evaluate" value="evaluate"><i class="far fa-paper-plane"></i> Submit Score</button>
				</div>
			</div>
		</div>
		</div>
		
		<!-- Comment Modal -->
		<div id="commentModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Comment</h4>
				</div>
                            <div class="modal-body"><?=htmlspecialchars_decode($submission['message'])?></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i class="far fa-window-close"></i> Close</button>
				</div>
			</div>
		</div>
		</div>
                
                <!-- Finalizer Modal -->
		<div id="awardModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Are you sure you want to award this bid?</h4>
				</div>
                            <div class="modal-body">After this bid is awarded, all other bids will be rejected for this opportunity.</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i class="far fa-window-close"></i> Cancel</button>
					<button type="submit" class="btn btn-success" name="award" value="award"><i class="far fa-money-bill-alt"></i> Award Bid</button>
				</div>
			</div>
		</div>
		</div>
		
	</div>
      
      <script>
        $(document).ready(function(){
            
            //Script to fix Summernote toolbar scroll bug
            $('.btn').click(function(){
                $('.note-toolbar-wrapper').css('height', 'auto');
            });
            
            //Summernote rich text initialization
            $('#evaluate-comment').summernote({
              toolbar: [
                  ['style', ['bold', 'italic', 'underline', 'clear']],
                  ['font', ['strikethrough', 'superscript', 'subscript']],
                  ['fontsize', ['fontsize']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['height', ['height']]
              ],
              placeholder: 'Enter a comment (optional)',
              dialogsInBody: true,
              tabsize: 2,
              disableResizeEditor: true,
              disableDragAndDrop: true,
              height: 250
          });
          $('#screen-comment').summernote({
              toolbar: [
                  ['style', ['bold', 'italic', 'underline', 'clear']],
                  ['font', ['strikethrough', 'superscript', 'subscript']],
                  ['fontsize', ['fontsize']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['height', ['height']]
              ],
              placeholder: 'Enter a comment (optional)',
              dialogsInBody: true,
              tabsize: 2,
              disableResizeEditor: true,
              disableDragAndDrop: true,
              height: 250
          });
          
          $('#remove-comment').summernote({
              toolbar: [
                  ['style', ['bold', 'italic', 'underline', 'clear']],
                  ['font', ['strikethrough', 'superscript', 'subscript']],
                  ['fontsize', ['fontsize']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['height', ['height']]
              ],
              placeholder: 'Enter a comment (required)',
              dialogsInBody: true,
              tabsize: 2,
              disableResizeEditor: true,
              disableDragAndDrop: true,
              height: 250
          });
          
          //Submission script, selects button and posts button value
             $(':submit').click(function(){
                 var clickBtnValue = $(this).val();
                 var $summernote = $('#'+clickBtnValue+'-comment');
                 var summernoteValue = $($summernote).val();
                 var score = $('#score').val();
                 var ajaxurl = 'action/submission_process.php',
                 data =  {'action': clickBtnValue,
                          'id': "<?=$submission_id?>",
                          'comment': summernoteValue,
                          'score': score
                };
                 $.post(ajaxurl, data, function (response){
                     if(response.includes('Success!')) location.reload();
                     alert(response);
                 });
             });
         });
      </script>

  </body>
</html>