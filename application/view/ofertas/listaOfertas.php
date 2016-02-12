<!-- cargamos la vista -->
<?php $this->layout('layout') ?>
<!-- Introducimos el buscador -->
<?php $this->insert('ofertas/buscadorOferta') ?>
<!-- Boton de crear -->
<div class="container borde">
    <a href="/Oferta" class="empresa">Volver</a>
</div>
<!-- contenedor del listado -->
<div class="container overflow">
    <!-- contenedor de feedback al usuario -->
    <?php $this->insert('partials/feedback') ?>
    <h2>Resultados</h2>
    <?php if(count($ofertas) == 0): ?>
        <p>No se encuentran Ofertas en la Base de Datos</p>
    <?php else: ?>
        <p>Total de ofertas: <b><?= count($ofertas) ?></b></p>
        <table>
        <thead>
            <td>ID</td>
            <td>Nombre</td>
            <td>Descripción</td>
            <td>Requisitos</td>
            <td>Salario</td>
            <td>URL</td>
            <td>Empresa</td>
            <td colspan="2">Acciones</td>
        </thead>
            <?php foreach($ofertas as $oferta): ?>
                <tr>
                    <td><?= $oferta['id'] ?></td>
                    <td><?= $oferta['nombre'] ?></td>
                    <td><?= $oferta['descripcion'] ?></td>
                    <td><?= $oferta['requisitos'] ?></td>
                    <td><?= $oferta['salario'] ?></td>
                    <td><a href="<?= $oferta['url'] ?>" target="_blank"><?= $oferta['url'] ?></td>
                    <td><?= $oferta['empresa'] ?></td>
                    <td><a href="/Oferta/editar/<?= $oferta['id'] ?>" class="accion">Editar</a></td>
                    <td><a href="/Oferta/borrar/<?= $oferta['id'] ?>" class="accion">Borrar</a></td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
</div>