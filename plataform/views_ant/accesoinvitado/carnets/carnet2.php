<div class="carnet2">
	<style>
		.carnet2 {		
										
		}		
			
		.carnet2 .body {
			display: flex;
			flex-direction: row;
			justify-content: space-around;			
			border: 1px solid gray;				
			width: 321px;
			height: 208px;
			background-color: white;			
		}
		.carnet2 .body .section1 {
			display: flex;
			flex-direction: column;
			justify-content: space-around;
		}	
		.carnet2 .body .section1 .logo-club{
			display: flex;
			justify-content: center;			
		}
		.carnet2 .body .section1 .logo-club img {
			height: 40px;
		}
		.carnet2 .body .section1 .foto-persona{			
			display: flex;
			justify-content: center;			
		}
		.carnet2 .body .section1 .foto-persona img {
			height: 100px;
			width: 100px;			
			border-radius: 1000px;
			border: 2px solid black;
		}
		.carnet2 .body .section1 .nombre-persona{
			display: flex;
			justify-content: center;			
		}
		.carnet2 .body .section1 .nombre-persona span{			
			font-size: 15px;
			font-weight: bold;
		}
		.carnet2 .body .section2 {
			display: flex;
			flex-direction: column;
			justify-content: center;
			font-size: 8;
			font-weight: bold;
		}
		.carnet2 .body .section2 .datos{
			display: flex;
			flex-direction: column;
			justify-content: space-around;
			align-items: center;
			font-size: 8px;
			font-weight: bold;
		}
		.carnet2 .body .section2 .codigo-qr{				
			display: flex;
			justify-content: center;					
		}
		.carnet2 .body .section2 .codigo-qr div img{
			width: 100px;
			height: 100px;
		}		
	</style>

	
		<div class="body">
			<div class="section1">
				<div class="logo-club">		
					<img src="<?php echo CLUB_ROOT.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );?>" height="40">			
				</div>
				<div class="foto-persona">				
					<img src="<?php echo $fotoPersona?>">								
				</div>				
				<div class="nombre-persona">
					<span>
						<?php echo $datos["nombre_persona"]?>
					</span>		
				</div>
			</div>
			<div class="section2">				
				<span><?php echo $datos["tipo"]?></span>
				<span>Acción <?php echo $datos["accion"]?></span>
				<span><?php if(!empty($datos["fecha_desde"] || !empty($datos["fecha_hasta"]))){ echo $datos["fecha_desde"];?> a <?php echo $datos["fecha_hasta"];}?></span>							
				<div class="codigo-qr">
					<div>
						<?php echo $qr?>
					</div>								
				</div>	
			</div>								
		</div>
	
	
</div>