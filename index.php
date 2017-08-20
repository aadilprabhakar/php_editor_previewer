<?php session_start();

if( isset( $_REQUEST['reset_session'] )):
	session_destroy();
	$header = $_SERVER['PHP_SELF'];
	if(!empty($_GET['file']))
	    $header .= '?file='.$_GET['file'];
	header('Location: ' .$header);
endif;

if( !isset( $_SESSION['tmp_file'] )):
    
    if(isset($_GET['file']) && !empty($_GET['file'])):
        $_SESSION['tmp_file']	=	'./'.$_GET['file'];
    else:
	    $_SESSION['tmp_file']	=	'./tmp/~'.time().'.php';
	endif;
	    
	$dhndl	=	fopen( $_SESSION['tmp_file'], 'a+' );
	fclose( $dhndl );
else:
	$dhndl		=	fopen( $_SESSION['tmp_file'], 'a+' );
	if( filesize( $_SESSION['tmp_file'] ) != 0 ):
		$content	=	fread( $dhndl, filesize( $_SESSION['tmp_file'] ) );
	endif;
	fclose( $dhndl );	
endif;

if( isset( $_REQUEST['save'] )):
	$data	=	$_POST['code'];
		
	$dhndl	=	fopen( $_SESSION['tmp_file'], 'w+' );
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

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" crossorigin="anonymous">

<style type="text/css">
	body,html		{ 	height:100%; padding:0px; margin:0px; 
							text-align:center; font-family:Tahoma, Geneva, sans-serif;	}
	div#status		{	width:180px; margin:0 auto; padding:5px 10px; background-color:#06F; color:#fff; 
						border-radius: 0 0 4px 4px; -moz-border-radius: 0 0 4px 4px; 
						-webkit-border-radius: 0 0 4px 4px;	-o-border-radius: 0 0 4px 4px; }
	
	section			{	border-radius: 10px; -moz-border-radius:10px; -webkit-border-radius:10px;
						-o-border-radius:10px; }							
							
	section#wrapper {	display:none; margin:10px auto; 
							min-height:600px; max-height:95%; height:800px; 
							background-color:#333; color:#CCC;
					}
	
	section#wrapper section#editor,
	section#wrapper section#preview {	
		display:inline-block; width:45%; min-width:400px;
		border:1px solid #ccc; height:90%; margin:2%;	
		background-color:#fff;
	}

	
	#editor textarea{	width:95%;	height:95%;	border:1px solid #ccc;	}
	
	iframe { 	width:100%; 	height:95%;	border:1px solid #ccc; background-color:#fff;	}

	#offcanvaseditor{ 
		position:absolute; margin:0; left:0; top:0; 
		background-color: rgba(85,85,85,0.95); 
		border-radius:5px; height:100%; padding:20px; width:90%; 
		z-index:9999999;
	}

	#offcanvaseditor #theForm,
	#offcanvaseditor #theform textarea ,
	#offcanvaseditor section{ height:100%; }
</style>
</head>
<body>
<div id="status">Loading...</div>

<div id="offcanvaseditor" style="">
<form id="theForm">
<section id="editor">
	<button id="saveandpreview" accesskey="s">Save &amp; Preview Code [Alt+S]</button> 
	<button accesskey="r" id="resetinterface">Reset Interface [Alt+R]</button>
	<button accesskey="h" id="hidex"> &lt;&lt; </button>
	<button accesskey="h" id="showex" class="float-right"> &gt;&gt; </button>
	<br /><br />
    <textarea name="code"><?php if(isset( $content )): echo $content; endif; ?></textarea>
</section>
</form>
</div>

<section id="wrapper" class="container">
	<br />
	<iframe id="phppreviewframe" src="" width="90%" height="99%" style="position:relative;display:block;"></iframe>
	<br />
</section><!-- #wrapper -->
    

<script src="https://code.jquery.com/jquery-3.2.1.min.js"  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"  crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" crossorigin="anonymous"></script>

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

	$('#hidex').click(function(E){
		E.preventDefault();
		w = $('#offcanvaseditor').width(); 
		w = (w*(97/100));
		$('#offcanvaseditor').animate({ left: '-'+w+'px' });
	});

	$('#showex').click(function(E){
		E.preventDefault();
		$('#offcanvaseditor').animate({ left: '0px' });
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

				$('#hidex').click();
			}
		});
	});
	
	$('section#wrapper').fadeIn('fast').end().find('div#status').html('<?php echo $_SESSION['tmp_file']; ?>').end().find('iframe#phppreviewframe').attr('src','<?php echo $_SESSION['tmp_file']; ?>');
});
</script>

</body>
</html>
