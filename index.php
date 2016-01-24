<?php
$allowed_extensions = array("gif", "jpg", "jpeg", "png");
// Directory where we're storing uploaded images
$upload_dir  = './upload_files';
$upload_name ='file_'.date("Y-m-d_His.");

if(isset($_GET['file_tree'])){
include(dirname(__FILE__) . "/extras/php_file_tree.php");
die( php_file_tree($upload_dir,"javascript:shoWImg('[link]',[id])", $allowed_extensions) );	
}

if(isset($_GET['uploadfile'])){
require(dirname(__FILE__) . '/extras/Uploader.php');

$Upload = new FileUpload('uploadfile');
$ext = $Upload->getExtension(); // Get the extension of the uploaded file
$Upload->newFileName = $upload_name.$ext;
$result = $Upload->handleUpload($upload_dir, $allowed_extensions);

if (!$result) 
    die( json_encode(array('success' => false, 'msg' => $Upload->getErrorMsg())) );   
 else 
    die( json_encode(array('success' => true, 'FileName' => $Upload->getFileName() ,'Size' => $Upload->getFileSize() ,'SavedFile' => $Upload->getSavedFile() , 'Extension' => $Upload->getExtension() ) ) );

}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تحميل الملفات</title>
	
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="./assets/css/bootstrap-rtl.min.css" rel="stylesheet">
    <link href="./assets/css/styles.css" rel="stylesheet">
	
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>  
    <script src="./assets/js/SimpleAjaxUploader.min.js"></script>
    <script src="./assets/js/php_file_tree_jquery.js"></script>
  </head>
  <body>

  

  <!-- Modal -->
  <div class="modal fade" id="ShowImage" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">عرض الصورة <code id="imgUrl"></code><mark id="imgNum" class="small pull-left"></mark></h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="imageId">
          <div id="img" class="upload-drop-zone" > </div>
		  
		  <span class="right upload-img-zone">
		    <button type="button" class="btn btn-default img-right btn-circle" id="previous" onclick="shoWImgNext('p')"></button>
		  </span>
		  
		  <span class="left upload-img-zone">
		    <button type="button" class="btn btn-default img-left btn-circle" id="next" onclick="shoWImgNext('n')"></button>
		  </span>
	
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">غلق</button>
        </div>
      </div>
      
    </div>
  </div>
  



  
      <div class="container">
        <div class="page-header">
          <h1>رفع ومعاينة الصور <code style="font-size: 14px;">النسخة 3</code></h1> 
        </div>
		
		
		 
		<?php if(isset($_GET['img'])){ ?>
		 <div class="upload-drop-zone col-md-12"  style="height: auto;" >
		  <img src="<?php echo $_GET['img']; ?>" class="img-rounded img-responsive" alt=""> 
     
         </div>
		<?php } else { ?>
	
  
  <div class="upload-drop-zone col-md-12"  style="z-index: 2;" >
     <div class="upload-drop-zone upload-drop-none col-md-12" style="z-index: 1;" id="dropzone">
	   <a href="#" id="uploadBtn" >اختار ملف</a> او قم باسقاطه هنا
	 </div>
  </div>


		    
          <div class="row"> 
		  
            <div class="col-xs-12" id="wrap">  
               
			   <h3 id="closeH3">الملفات المرفوعة : <a href="#" class="close" id="button1">&times;</a></h3>
			
	           <div id="pic-progress-wrap" class="progress-wrap" style="margin-bottom:10px;"></div>			
			   <ul class="list-group" style="margin-top:10px;" > <div id="msgBox"></div> </ul>		  
            </div>
          </div>
		  
		  <!-- <hr> -->
		  <h3>الجواب :</h3>
		 <div id="Json"> </div>
		  
		<!-- <hr> -->
		 <h3>استكشاف الملفات :</h3>
		<div id="Browing">

		</div>
		

		  
		<?php }?>
		  
      </div>
	  


	  <br><br>

<script>

$('#button1').click(function() {
    $('#wrap').hide();
});

function escapeTags( str ) {
  return String( str )
           .replace( /&/g, '&amp;' )
           .replace( /"/g, '&quot;' )
           .replace( /'/g, '&#39;' )
           .replace( /</g, '&lt;' )
           .replace( />/g, '&gt;' );
}


function ExTFileName(img)
{
var file_name_array = img.split("/");
return file_name_array[file_name_array.length - 1];
}

function GetFileNameFromHref(id)
{
	var a_href = $('#FileTreeId_'+id).attr('href');
    a_href = a_href.split("'");
	return a_href[1];
}

function disabled_btn(id)
{
 if(id==1)
	{
	$("#previous").attr('disabled','disabled');	
	$("#next").removeAttr('disabled');
	} else{
	$("#previous").removeAttr('disabled');
	};	
	if( id==getMaxId() )
	{
	$("#next").attr('disabled','disabled');	
	$("#previous").removeAttr('disabled');
	} else{
	$("#next").removeAttr('disabled');	
	}
}
function getMaxId()
{
	var max = $( ".FileTree" ).last().attr('id');
    return  max.replace("FileTreeId_", "");	
}

function shoWImg(img,id)
{
	 $('#imageId').val(id);
	 $('#ShowImage').modal('show');
	 $("#img").html('<img src="'+img+'" class="img-rounded img-responsive" alt="">'); 
	 $("#imgUrl").html(ExTFileName(img)); 
	 disabled_btn(id);
	 $("#imgNum").html(''); 
	 
}

function shoWImgNext(type)
{	
   var id=$('#imageId').val();
   
	if(type=='n')
	 id=parseInt(id)+1;
	else
	 id=parseInt(id)-1;
 
	if(id<1) id=1;
	//if ($('#FileTreeId_'+id).length == false) {id=1;}
	
	$("#imgNum").html(id +' / ' +getMaxId()); 
	disabled_btn(id);
		
    $('#imageId').val(id);

	a_href = GetFileNameFromHref(id);	
	
	$("#img").html('<img src="'+a_href+'" class="img-rounded img-responsive" alt="">'); 
	$("#imgUrl").html(ExTFileName(a_href)); 
}
window.onload = function() {

	$('#wrap').hide();
    var dropZone = document.getElementById('dropzone');

    dropZone.ondrop = function(e) {
        e.preventDefault();
        this.className = 'upload-drop-zone';
        
    };

    dropZone.ondragover = function() {
        this.className = 'upload-drop-zone drop';
        return false;
    };

    dropZone.ondragleave = function() {
        this.className = 'upload-drop-zone';
        return false;
    };

	
	
  $("#Browing").load('?file_tree');
  var btn = document.getElementById('uploadBtn'),
	  wrap = document.getElementById('pic-progress-wrap'),
      msgBox = document.getElementById('msgBox');

 
  var uploader = new ss.SimpleUpload({
        button: btn,
        url: '?uploadfile',
        name: 'uploadfile',
        multipart: true,
        hoverClass: 'hover',
        focusClass: 'focus',
        responseType: 'json',
		multiple: true,
		customHeaders: {'Authorization': 'my-access-token'},
		dropzone: 'dropzone', // ID of element to be the drop zone
        startXHR: function() {
            wrap.style.display = 'block'; // make progress bar visible
			$('#wrap').show();

        },
       onSubmit: function(filename, ext) {            
           var prog = document.createElement('div'),
               outer = document.createElement('div'),
               bar = document.createElement('div'),
               size = document.createElement('div'),
               self = this;     
    
            prog.className = 'prog';
            size.className = 'size';
            outer.className = 'progress progress-striped';
            bar.className = 'progress-bar progress-bar-success';
            
            outer.appendChild(bar);
            prog.appendChild(size);
            prog.appendChild(outer);
            wrap.appendChild(prog); // 'wrap' is an element on the page
            
            self.setProgressBar(bar);
            self.setProgressContainer(prog);
            self.setFileSizeBox(size);                
            
            btn.innerHTML = 'جاري التحميل ..';
          },		
        onComplete: function( filename, response ) {
            btn.innerHTML = 'اختار ملف اخر';
            wrap.style.display = 'none'; // hide progress bar when upload is completed
//alert(response.toSource());

           $("#Json").html('<code>'+response.toSource()+'</code>');
		   
            if ( !response ) {
                $("#msgBox").append('<li class="list-group-item list-group-item-danger"><span class="badge alert-danger pull-left">خطأ</span>لا يمكن تحميل الملف/ات</li>');
                return;
            }

            if ( response.success === true ) {
				
                $("#msgBox").append('<li class="list-group-item list-group-item-success"><span class="badge alert-success pull-left">تم بنجاح</span>' +
				                       '<a href="javascript:shoWImg('+"'<?php echo $upload_dir.'/'; ?>"+ response.FileName +"'"+',-1)">'+escapeTags( filename )+'</a>'+
				                    '</li>');
				$("#Browing").load('?file_tree');
				

            } else {
                if ( response.msg )  {
                    $("#msgBox").append('<li class="list-group-item list-group-item-danger"><span class="badge alert-danger pull-left">خطأ</span>' + escapeTags( response.msg ) + '</li>');

                } else {
                    $("#msgBox").append('<li class="list-group-item list-group-item-danger"><span class="badge alert-danger pull-left">خطأ</span>حدث خطأ اثناء رفع الملف/ات</li>');
                }
            }
          },
        onError: function() {
			btn.innerHTML = 'اختار ملف اخر';
            wrap.style.display = 'none';
            $("#msgBox").append('<li class="list-group-item list-group-item-danger"><span class="badge alert-danger pull-left">خطأ</span>لا يمكن تحميل الملف/ات</li>');
          }
	});
};
</script>
  </body>
</html>