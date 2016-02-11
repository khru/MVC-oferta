<?php $this->layout('layout') ?>
<div class="container">
	<h2><?= isset($titulo) ? $titulo : 'Alta de usuario'; ?></h2>
    <?php $this->insert('partials/feedback') ?>
    <form action="/Usuario/alta" method="post" class="login">
        <section>
        	<p>
        		<label for="nombre">Nombre: (*)</label><input type="text" name="nombre" value="<?= isset($datos['nombre']) ? $datos['nombre'] : '' ; ?>" placeholder="Emmanuel" required autofocus pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ ]{3,50}">
        	</p>
        	<p>
        		<label for="apellido">Apellidos: (*)</label><input type="text" name="apellido" value="<?= isset($datos['apellido']) ? $datos['apellido'] : '' ; ?>" placeholder="Valverde Ramo" required autofocus pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ ]{3,50}">
        	</p>
            <p>
                <label for="email">Email: (*)</label><input type="email" name="email" value="<?= isset($datos['email']) ? $datos['email'] : '' ; ?>" placeholder="admin@admin.com" required autofocus>
            </p>
            <p>
                <label for="clave">Clave: (*)</label><input type="password" name="clave" value="" placeholder="Password1" required autofocus Pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ0-9]{6,50}">
            </p>
            <p>
                <label for="claveRe">Clave Repetida: (*)</label><input type="password" name="claveRe" value="" placeholder="Password1" required autofocus Pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ0-9]{6,50}">
            </p>
            <p>
                <input type="submit" name="login" value="Enviar">
            </p>
        </section>
    </form>
</div>