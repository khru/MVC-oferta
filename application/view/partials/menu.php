<div class="navigation">
    <a href="<?php echo URL; ?>">Inicio</a>

    <!-- Enlaces para usuarios logueados en la aplicación -->
    <?php if(Session::get('user_logged_in')) :?>
    	<a href="<?php echo URL; ?>Empresa">Empresas</a>
    	<a href="<?php echo URL; ?>Oferta">Ofertas</a>
	<?php endif; ?>

    <!-- Enlaces para usuarios no logueados en la aplicación -->
    <?php if(!Session::get('user_logged_in')) :?>
        <a href="<?php echo URL; ?>Login">Login</a>
    	<a href="<?php echo URL; ?>Usuario">Alta</a>
    <?php endif; ?>

    <!-- Enlaces para usuarios logueados en la aplicación -->
    <?php if(Session::get('user_logged_in')) :?>
    	<a href="<?php echo URL; ?>Login/salir">Salir</a>
    <?php endif; ?>
</div>