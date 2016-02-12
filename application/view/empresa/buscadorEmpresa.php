<div class="container buscador borde">
	<form action="/Empresa/buscar" name="buscarEmpresa" method="POST">
	<p>
		<input type="search" name="busqueda" class="bus" value="<?= isset($busqueda) ? $busqueda : ''; ?>" placeholder="Buscar...">
	</p>
	<p>
		<input type="submit" name="buscarEmpresa" value="Buscar">
	</p>
	</form>
</div>