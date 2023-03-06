<div class="carnet1">
	<style>
		.carnet1 {
										
		}		
				
		.carnet1  .body {
			display: flex;
			flex-direction: column;
			justify-content: space-around;			
			width: 100%;
			height: 100%;
			border: 1px solid gray;				
			width: 397px;
			height: 548px;
			background-color: white;			
		}
		.carnet1  .body .logo-club{
			display: flex;
			justify-content: center;
		}
		.carnet1 .body .foto-persona{			
			display: flex;
			justify-content: center;			
		}
		.carnet1 .body .foto-persona img {
			height: 150px;
			width: 150px;			
			border-radius: 1000px;
			border: 2px solid black;
		}
		.carnet1 .body .nombre-persona{
			display: flex;
			justify-content: center;
			font-size: 20px;
			font-weight: bold;
		}
		.carnet1 .body .datos{
			display: flex;
			flex-direction: column;
			justify-content: space-around;
			align-items: center;
			font-size: 16px;
			font-weight: bold;
		}
		.carnet1 .body .codigo-qr{				
			display: flex;
			justify-content: center;					
		}
		.carnet1 .body .codigo-qr div img{
			width: 170px;
			height: 170px;
		}		
	</style>
	
		<div class="body">			
			<div class="logo-club">		
				<img src="<?php echo CLUB_ROOT.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );?>" height="80px">			
			</div>
			<div class="foto-persona">				
				<img src="<?php echo $fotoPersona?>">								
			</div>				
			<div class="nombre-persona">
				<?php echo $datos["nombre_persona"]?>		
			</div>
			<div class="datos">
				<span><?php echo $datos["tipo"]?></span>
				<span>Acción <?php echo $datos["accion"]?></span>
				<span><?php if(!empty($datos["fecha_desde"] || !empty($datos["fecha_hasta"]))){ echo date("d-m-Y", strtotime($datos["fecha_desde"]));?> a <?php echo date("d-m-Y", strtotime($datos["fecha_hasta"]));;}?></span>
				<div class="codigo-qr">
					<div>
					<?php echo $qr;?>
					</div>		
								
				</div>
			</div>				
		</div>
	
	
</div>