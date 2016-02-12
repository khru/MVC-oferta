<?php $this->layout('layout') ?>
<!-- Introducimos el buscador -->
<?php if(isset($busqueda)) : $busqueda = ['busqueda' => $busqueda];?>
<?php $this->insert('empresa/buscadorEmpresa', $busqueda) ?>
<?php else: ?>
<?php $this->insert('empresa/buscadorEmpresa') ?>
<?php endif; ?>
<!-- Boton de crear -->
<div class="container borde">
    <a href="/Empresa/crear" class="empresa">Crear Empresa</a>
</div>
<!-- contenedor del listado -->
<div class="container">
    <?php $this->insert('partials/feedback') ?>
    <h2>Lista de empresas</h2>
    <?php if(count($empresas) == 0): ?>
        <p>No se encuentran empresas en la Base de Datos</p>
    <?php else: ?>
        <p>Total de empresas: <b><?= count($empresas) ?></b></p>
        <?php foreach($empresas as $empresa): ?>
            <article class="pregunta" id="<?= $empresa['id'] ?>">
                <h3><?= $empresa['nombre'] ?></h3>
                <p><b>Enlace:</b> <a href="<?= $empresa['web'] ?>" target="_blank"><?= $empresa['web'] ?></a></p>
                <p><b>Descripción:</b> <?= $empresa['descripcion'] ?></p>
                <footer>
                    <a href="/Empresa/editar/<?= $empresa['id'] ?>" class="accion">Editar</a>
                    <a href="/Empresa/borrar/<?= $empresa['id'] ?>" class="accion">Borrar</a>
                </footer>
            </article>
        <?php endforeach ?>
    <?php endif ?>
</div>