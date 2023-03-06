<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src='say-cheese.js'></script>
<script>
$(document).ready(function(){
    var sayCheese = new SayCheese('#container-element', { snapshots: true });
    
    sayCheese.on('start', function() {
     // do something when started
     this.takeSnapshot();
    });
    
    sayCheese.on('error', function(evt, error) {
     // handle errors, such as when a user denies the request to use the webcam,
     // or when the getUserMedia API isn't supported
    });
    
    sayCheese.on('snapshot', function(evt, snapshot) {
      // do something with a snapshot canvas element, when taken
    });
    
    sayCheese.start();
    
    $('button').click(function(){
        alert("works");
      sayCheese.takeSnapshot();
    });
	
	var sayCheese = new SayCheese('#element', { snapshots: true });

sayCheese.on('start', function() {
 // do something when started
 this.takeSnapshot();
});

sayCheese.on('error', function(evt, error) {
 // handle errors, such as when a user denies the request to use the webcam,
 // or when the getUserMedia API isn't supported
});

sayCheese.on('snapshot', function(evt, snapshot) {
  // do something with a snapshot canvas element, when taken
});

sayCheese.start();
});



</script>
<style>
 
#container-element {
  height: 300px;
  width: 300px;
  background-color: #333;
  margin: 0 auto;
}
</style>
</head>

<body>

<button>Take a picture.</button>

<div id="container-element">
</div>


</body>
</html>