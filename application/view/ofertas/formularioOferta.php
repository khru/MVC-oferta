<?php $this->layout('layout') ?>
<div class="container">
	<h2><?= isset($titulo) ? $titulo : 'Creación de oferta'; ?></h2>
    <?php $this->insert('partials/feedback') ?>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="login">
        <section>
        	<p>
        		<label for="nombre">Nombre: (*)</label><input type="text" name="nombre" value="<?= isset($datos['nombre']) ? $datos['nombre'] : '' ; ?>" placeholder="Everis" required autofocus pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ #-_)( ]{3,50}">
        	</p>
            <p>
                <label for="descripcion">Descripción: (*)</label><textarea name="descripcion" id="descripcion" cols="30" rows="10" required autofocus><?= isset($datos['descripcion']) ? $datos['descripcion'] : '' ; ?></textarea>
            </p>
            <p>
                <label for="requisitos">Requisitpos: (*)</label><textarea name="requisitos" id="requisitos" cols="30" rows="10" required autofocus><?= isset($datos['requisitos']) ? $datos['requisitos'] : '' ; ?></textarea>
            </p>
            <p>
                <label for="salario">Salario:</label><input type="text" name="salario" value="<?= isset($datos['salario']) ? $datos['salario'] : '' ; ?>" placeholder="30.000 - 40.000€ aunales">
            </p>
        	<p>
        		<label for="url">URL: (*) Debe empezar con http:// o https://</label><input type="url" name="url" value="<?= isset($datos['url']) ? $datos['url'] : '' ; ?>" placeholder="https://www.infojobs.net/barcelona/programador-php-senior/of-ib1e32b1b8b48aea6594af56a3a165e" required autofocus pattern="https?://.+">
        	</p>
            <p>
                <?php  $datos = ['datos' => $empresas] ?>
                <?php $this->insert('ofertas/selectEmpresa', $datos) ?>
            </p>
            <p>
                <input type="submit" name="login" value="Enviar">
            </p>
        </section>
    </form>
</div>