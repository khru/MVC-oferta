<div class="container buscador borde">
	<form action="/Oferta/buscar" name="buscarOferta" method="POST">
	<p>
		<input type="search" name="busqueda" value="<?= isset($busqueda) ? $busqueda : ''; ?>" placeholder="Buscar...">
	</p>
	<p>
		<input type="submit" name="buscarOferta" value="Buscar">
	</p>
	</form>
</div>