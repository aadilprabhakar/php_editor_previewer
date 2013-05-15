<?php session_start();

if( isset( $_REQUEST['reset_session'] )):
	session_destroy();
	header('Location: ' . $_SERVER['PHP_SELF']);
endif;

if( !isset( $_SESSION['tmp_file'] )):
	$_SESSION['tmp_file']	=	'./tmp/~'.time().'.php';
	$dhndl	=	fopen( $_SESSION['tmp_file'], 'w' );
	fclose( $dhndl );
else:
	$dhndl		=	fopen( $_SESSION['tmp_file'], 'w' );
	if( filesize( $_SESSION['tmp_file'] ) != 0 ):
		$content	=	fread( $dhndl, filesize( $_SESSION['tmp_file'] ) );
	endif;
	fclose( $dhndl );	
endif;

if( isset( $_REQUEST['save'] )):
	$data	=	$_POST['code'];
		
	$dhndl	=	fopen( $_SESSION['tmp_file'], 'w' );
	fwrite( $dhndl, $data );
	fclose( $dhndl );
	
	echo 'Success';
	exit();
endif;
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Editor &amp; Previewer</title>
<style type="text/css">
	body,html		{ 	height:100%; padding:0px; margin:0px; 
							text-align:center; font-family:Tahoma, Geneva, sans-serif;	}
	div#status		{	width:180px; margin:0 auto; padding:5px 10px; background-color:#06F; color:#fff; 
						border-radius: 0 0 4px 4px; -moz-border-radius: 0 0 4px 4px; 
						-webkit-border-radius: 0 0 4px 4px;	-o-border-radius: 0 0 4px 4px; }
	
	section			{	border-radius: 10px; -moz-border-radius:10px; -webkit-border-radius:10px;
						-o-border-radius:10px; }							
							
	section#wrapper {	display:none; margin:10px auto; 
							min-width:900px; max-width:95%; width:auto;
							min-height:600px; max-height:95%; height:800px; 
							background-color:#333; color:#CCC;}
	
	section#wrapper section#editor,
	section#wrapper section#preview {	
		display:inline-block; width:45%; min-width:400px;
		border:1px solid #ccc; height:90%; margin:2%;	
		background-color:#fff;
	}

	section#editor	{	float:left;		}	
	section#preview {	float:right;	}
	
	#editor textarea{	width:90%;	height:90%;	border:1px solid #ccc;	}
	#preview iframe { 	width:90%; 	height:90%;	border:1px solid #ccc;	}
</style>
</head>
<body>
<div id="status">Loading...</div>

<form id="theForm">
<section id="wrapper">
<section id="editor">
	<button id="saveandpreview" accesskey="s">Save &amp; Preview Code [Alt+S]</button> <button accesskey="r" id="resetinterface">Reset Interface [Alt+R]</button><br />
    <textarea name="code"><?php if(isset( $content )): echo $content; endif; ?></textarea>
</section>
<section id="preview">
	<div id="loadHere">
    	<button disabled>PHP Preview Area</button>
    	<iframe id="phppreviewframe" src="" width="90%" height="90%"></iframe>
    </div>
</section>
</section><!-- #wrapper -->
</form>
    
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){

	$("textarea").keydown(function(e) {
	  var $this, end, start;
	  if (e.keyCode === 9) {
		start = this.selectionStart;
		end = this.selectionEnd;
		$this = $(this);
		$this.val($this.val().substring(0, start) + "\t" + $this.val().substring(end));
		this.selectionStart = this.selectionEnd = start + 1;
		return false;
	  }
	});
	
	$('button#resetinterface').click( function(e){
		e.preventDefault();
		window.location = '<?php echo $_SERVER['PHP_SELF']; ?>?reset_session=1';
	});
	
	$('button#saveandpreview').click(function(e){
		e.preventDefault();
		var	theform	=	'form#theForm';
		$.ajax({
			url:	'<?php echo $_SERVER['PHP_SELF']; ?>?save=true',
			data:	$(theform).serialize(),
			type:	'POST',
			success:function(sucmsg){
				//alert(sucmsg);	
				$('iframe#phppreviewframe').attr('src','<?php echo $_SESSION['tmp_file']; ?>').end().find('div#status').html('Saved...').end();
			}
		});
	});
	
	$('section#wrapper').fadeIn('fast').end().find('div#status').html('<?php echo $_SESSION['tmp_file']; ?>').end().find('iframe#phppreviewframe').attr('src','<?php echo $_SESSION['tmp_file']; ?>');
});
</script>
</body>
</html>