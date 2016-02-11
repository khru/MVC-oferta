<?php $this->layout('layout') ?>
<div class="container">
    <h2><?= isset($titulo) ? $titulo : 'Login'; ?></h2>
    <?php $this->insert('partials/feedback') ?>
    <form action="/Login/dologin" method="post" class="login">
        <section>
            <p>
                <label for="email">Email:</label><input type="email" name="email" value="<?= isset($datos['email']) ? $datos['email'] : '' ; ?>" required autofocus placeholder="admin@admin.com">
            </p>
            <p>
                <label for="clave">Clave:</label><input type="password" name="clave" value="" placeholder="Password1" required autofocus Pattern="[a-zA-ZÑñÁáÉéíÍÓóÚúÀàèìòùÈÌÒÙäëïöüäëïöüÄËÏÖÜ0-9]{6,50}">
            </p>
            <p>
                <input type="submit" name="login" value="Acceder">
            </p>
        </section>
    </form>
</div>