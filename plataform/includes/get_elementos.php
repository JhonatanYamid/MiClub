<table data-role="table" id="estudios-table" data-mode="reflow" class="ui-responsive table-stroke">
	  <thead>
	    <tr>
			
	      	<th data-priority="persist">Seleccione un <?=$datos_servicio["LabelElemento"] ?></th>
	    </tr>
	  </thead>
	  <tbody>
		<?
		foreach( $array_elementos["response"] as $key => $datos_elemento  )
		{
		?>
			<tr>
				
				<td>
					<a href="reserva_horas.php?id=<?=$datos_elemento["IDElemento"] ?>" data-ajax="false"><?=$datos_elemento["Nombre"] ?></a>
				</td>
				
			</tr>
		<?	
		}//end for
		?>
	</tbody>
</table>