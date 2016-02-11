<?php $this->layout('layout') ?>
<div class="container">
	<h2><?= isset($titulo) ? $titulo : 'Creación de oferta'; ?></h2>
    <?php $this->insert('partials/feedback') ?>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="login">
        <section>
        	<p>
        		<label for="nombre">Nombre: (*)</label><input type="text" name="nombre" value="<?= isset($datos['nombre']) ? $datos['nombre'] : '' ; ?>" placeholder="Everis" required autofocus pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ _#-_)( ]{3,50}">
        	</p>
        	<p>
        		<label for="web">Web: (*) Debe empezar con http:// o https://</label><input type="url" name="web" value="<?= isset($datos['web']) ? $datos['web'] : '' ; ?>" placeholder="http://www.everis.com/" required autofocus pattern="https?://.+">
        	</p>
            <p>
                <label for="descripcion">Descripción: (*)</label><textarea name="descripcion" id="descripcion" cols="30" rows="10" required autofocus><?= isset($datos['descripcion']) ? $datos['descripcion'] : '' ; ?></textarea>
            </p>
            <p>
                <input type="submit" name="login" value="Enviar">
            </p>
        </section>
    </form>
</div>