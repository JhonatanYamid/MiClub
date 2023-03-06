 <?php 
 require(dirname(__FILE__) . "/../../admin/config.inc.php");
 
  if(!isset($_GET['IDSocio'])){
    $resultado12="Lo sentimos, usted no pertenece a nuestro club!";
  }else{  
  $IDSocio= $_GET['IDSocio'];
  
  
                                $datos_socios = "SELECT * FROM Socio WHERE IDSocio=$IDSocio LIMIT 1";
                                $datos = $dbo->query($datos_socios);
                                while ($row = $dbo->fetchArray($datos)) {
                                    $token = $row["TokenCountryMedellin"];
                                    $codigo = $row["NumeroDerecho"];
                                }
                                
                                require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";                                     
        
                                $resultado = SIMWebServiceCountryMedellin::App_ConsultarPerfil($token);
                                $data1 = json_decode($resultado);
                                $dato =  $data1->perfil->tiposocio;
                                $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarRedes($token);
                                $resultado2 = str_replace("{", "", $resultado1);
                                $resultado3 = str_replace("}", "", $resultado2);
                                $resultado4 = str_replace("[", "", $resultado3);
                                $resultado5 = str_replace("]", "", $resultado4);
                                $resultado6 = str_replace('"0":', "", $resultado5);
                                $resultado7 = str_replace('"\"', "", $resultado6);
                                $resultado8 = str_replace('\"', "", $resultado7);
                                $resultado9 = str_replace(',', "   ", $resultado8);
                                $resultado10 = str_replace('"', "", $resultado9);
                                $resultado11 = str_replace('nombre:', "🌐", $resultado10);
                                $resultado12 = str_replace('contrasena:', "\n🔐", $resultado11);
                                
  
  
           } 


 
 ?>
 
 
 <html lang="fr">
<head>
	<!--
		https://dribbble.com/shots/2001637-Article-News-Card-UI
		http://www.grafikart.fr/tutoriels/html-css/card-ui-629
	-->
	<meta charset="UTF-8">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/style.min.css">
</head>
<body>
	<article class="card">
		 
		<div class="card__date">
			<span class="card__date__day"><?php echo date("j");?></span>
			<span class="card__date__month"><?php echo date("M");?></span>
		</div>
		<div class="card__body">
			<div class="card__category"><a href="#">Red Wifi</a></div>
			<div class="card__title"><a href="#">Información de red</a> <br><br>
			</div>
			<?php  echo  $resultado12; ?> 
 
		</div>
		<footer class="card__footer"> 
			<span class="icon icon--comment"><a href="#">CCE</a></span>
		</footer>
	</article>
</body>
</html>
<style>
@charset "UTF-8";
* {
  box-sizing: border-box;
}

body {
  font-family: "Open sans";
  font-size: 36px;
  line-height: 1.4;
  background: #d8e0e5;
}

.card {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 700px;
  height: 400px;
  transform: translateX(-50%) translateY(-50%);
  background: #fff;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s;
}
.card:hover {
  box-shadow: 0 0 50px rgba(0, 0, 0, 0.3);
}
.card a {
  color: inherit;
  text-decoration: none;
}

.card__date {
  background: #00786A;
  position: absolute;
  top: 20px;
  right: 20px;
  width: 90px;
  height: 90px;
  border-radius: 50%;
  color: #fff;
  text-align: center;
  line-height: 23px;
  font-weight: bold;
  padding-top: 20px;
}
.card__date__day {
  display: block;
  font-size: 28px;
}
.card__date__month {
  display: block;
  font-size: 20px;
  text-transform: uppercase;
}

.card__thumb {
  height: 235px;
  overflow: hidden;
  background-color: #000;
  transition: height 0.3s;
}
.card__thumb img {
  display: block;
  opacity: 1;
  transition: opacity 0.3s, transform 0.3s;
  transform: scale(1);
}
.card:hover .card__thumb img {
  opacity: 0.6;
  transform: scale(1.2);
}
.card:hover .card__thumb {
  height: 90px;
}

.card__body {
  position: relative;
  padding: 20px;
  height: 185px;
  transition: height 0.3s;
}
.card:hover .card__body {
  height: 330px;
}

.card__category {
  position: absolute;
  top: -15px;
  left: 0;
  height: 35px;
  padding: 0 15px;
  background: #00786A;
  color: #fff;
  font-size: 30px;
  line-height: 25px;
}
.card__category a {
  color: #fff;
  text-decoration: none;
  text-transform: uppercase;
}

.card__title {
  padding: 0 0 10px 0;
  margin: 0;
  font-size: 38px;
  color: #000;
  font-weight: bold;
}
.card:hover .card__title {
  -webkit-animation: titleBlur 0.3s;
          animation: titleBlur 0.3s;
}

.card__subtitle {
  margin: 0;
  padding: 0 0 10px 0;
  color: #08c;
  font-size: 35px;
}
.card:hover .card__subtitle {
  -webkit-animation: subtitleBlur 0.3s;
          animation: subtitleBlur 0.3s;
}

.card__description {
  position: absolute;
  left: 20px;
  right: 20px;
  bottom: 65px;
  margin: 0;
  padding: 0;
  color: #666c74;
  font-size: 34px;
  line-height: 27px;
  opacity: 0;
  transition: opacity 0.2s, transform 0.2s;
  transition-delay: 0s;
  transform: translateY(25px);
}
.card:hover .card__description {
  opacity: 1;
  transition-delay: 0.1s;
  transform: translateY(0);
}

.card__footer {
  position: absolute;
  color: #a3a9ab;
  bottom: 20px;
  left: 20px;
  right: 20px;
  font-size: 11px;
}
.card__footer .icon--comment {
  margin-left: 10px;
}

.icon {
  display: inline-block;
  vertical-align: middle;
  margin-right: 2px;
}
.icon:before {
  display: inline-block;
  text-align: center;
  height: 14px;
  width: 14px;
  margin-top: -2px;
  margin-right: 6px;
}

.icon--comment:before {
  content: "";
  display: inline-block;
  font: normal normal normal 16px/1 FontAwesome;
  text-rendering: auto;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  transform: translate(0, 0);
}

.icon--time:before {
  content: "";
  display: inline-block;
  font: normal normal normal 16px/1 FontAwesome;
  text-rendering: auto;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  transform: translate(0, 0);
}

@-webkit-keyframes titleBlur {
  0% {
    opacity: 0.6;
    text-shadow: 0px 5px 5px rgba(0, 0, 0, 0.6);
  }
  100% {
    opacity: 1;
    text-shadow: 0px 5px 5px rgba(0, 0, 0, 0);
  }
}

@keyframes titleBlur {
  0% {
    opacity: 0.6;
    text-shadow: 0px 5px 5px rgba(0, 0, 0, 0.6);
  }
  100% {
    opacity: 1;
    text-shadow: 0px 5px 5px rgba(0, 0, 0, 0);
  }
}
@-webkit-keyframes subtitleBlur {
  0% {
    opacity: 0.6;
    text-shadow: 0px 5px 5px rgba(0, 136, 204, 0.6);
  }
  100% {
    opacity: 1;
    text-shadow: 0px 5px 5px rgba(0, 136, 204, 0);
  }
}
@keyframes subtitleBlur {
  0% {
    opacity: 0.6;
    text-shadow: 0px 5px 5px rgba(0, 136, 204, 0.6);
  }
  100% {
    opacity: 1;
    text-shadow: 0px 5px 5px rgba(0, 136, 204, 0);
  }
}
</style>
