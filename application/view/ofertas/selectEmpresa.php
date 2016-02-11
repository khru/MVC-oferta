<label for="empresa">Seleccione de que empresa es la oferta:  </label>
<?php if(empty($datos)): ?>
	<select name="empresa" id="empresa">
		<option value="">NO HAY EMPRESAS</option>
	</select>
<?php else : ?>
	<select name="empresa" id="empresa">
		<?php foreach ($datos as $key => $value) :?>
			<?php if($value['empresa'] == Session::get('selected')) : ?>
				<option selected value="<?= $value['empresa']; ?>"><?= $value['empresa']; ?></option>
			<?php else: ?>
				<option value="<?= $value['empresa']; ?>"><?= $value['empresa']; ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
<?php endif; ?>
<?php Session::delete('selected'); ?>
