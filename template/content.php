
<form role="form" action="data.php" method="post">
	<fieldset >
		<div class="form-group">
		      <label for="">Introduzque la bacteria por buscar</label>
		<input name="bacteria" id="bacteria" class="form-control input-lg" type="text" value="" class="span3" style="margin: 0 auto;" data-provide="typeahead" data-items="4" data-source="[
		<?php
			$species = "";
			foreach ($bacteria->scanDir() as $key => $value) {
				$species = $species."&quot;".$value."&quot;,";
			}
			echo substr($species, 0,-1);
		?>
		]">

		</div>
	<button type="submit" class="btn btn-primary btn-lg">Guardar localmente</button>
	<!--<a href="#" class="btn btn-info btn-lg" role="button">Ver datos</a>-->

	</fieldset>
</form>



