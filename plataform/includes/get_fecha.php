<form name="frmGeneral" id="frmGeneral"  method="post" action="index.php" data-ajax="false" >
		
	    <p>
	    	<label for="field1">Fecha</label>
	    	<input name="fecha" id="fecha" type="date" data-role="datebox" data-options='{"mode": "calbox","themeDatePick":"c","themeHeader":"c","themeDate":"a"}' data-theme="c">
	    </p>
	    
	    <p class="submit">
	    	<input type="hidden" name="action" value="buscar">
			<input type="submit" value="Buscar" name="submit" data-role="button"   data-theme="b">
		</p>
</form>