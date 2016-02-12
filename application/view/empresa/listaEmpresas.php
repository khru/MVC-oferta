<!-- cargamos la vista -->
<?php $this->layout('layout') ?>
<!-- Introducimos el buscador -->
<?php $this->insert('empresa/buscadorEmpresa') ?>
<!-- Boton de crear -->
<div class="container borde">
    <a href="/Empresa" class="empresa">Volver</a>
</div>
<!-- contenedor del listado -->
<div class="container overflow">
    <!-- contenedor de feedback al usuario -->
    <?php $this->insert('partials/feedback') ?>
    <h2>Resultados</h2>
    <?php if(count($empresas) == 0): ?>
        <p>No se encuentran empresas en la Base de Datos</p>
    <?php else: ?>
        <p>Total de empresas: <b><?= count($empresas) ?></b></p>
        <table>
        <thead>
            <td>ID</td>
            <td>Nombre</td>
            <td>Web</td>
            <td>Descripción</td>
            <td colspan="2">Acciones</td>
        </thead>
            <?php foreach($empresas as $empresa): ?>
                <tr>
                    <td><?= $empresa['id'] ?></td>
                    <td><?= $empresa['nombre'] ?></td>
                    <td><?= $empresa['descripcion'] ?></td>
                    <td><a href="<?= $empresa['web'] ?>" target="_blank"><?= $empresa['web'] ?></td>
                    <td><a href="/Empresa/editar/<?= $empresa['id'] ?>" class="accion">Editar</a></td>
                    <td><a href="/Empresa/borrar/<?= $empresa['id'] ?>" class="accion">Borrar</a></td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
</div>