<?php $this->layout('layout') ?>
<!-- Introducimos el buscador -->
<?php if(isset($busqueda)) : $busqueda = ['busqueda' => $busqueda];?>
    <?php $this->insert('ofertas/buscadorOferta', $busqueda) ?>
<?php else: ?>
    <?php $this->insert('ofertas/buscadorOferta') ?>
<?php endif; ?>
<div class="container borde">
    <a href="/Oferta/crear" class="empresa">Crear Oferta</a>
</div>
<div class="container">
    <?php $this->insert('partials/feedback') ?>
    <h2>Lista de Ofertas</h2>
    <?php if(count($ofertas) == 0): ?>
        <p>No se encuentran ofertas en la Base de Datos</p>
    <?php else: ?>
        <p>Total de ofertas: <b><?= count($ofertas) ?></b></p>
        <?php foreach($ofertas as $oferta): ?>
            <article class="pregunta" id="<?= isset($oferta['id']) ? $oferta['id'] : ''; ?>">
                <h3><?= $oferta['nombre'] ?></h3>
                <p><b>Fecha de creación: </b><date><?= $oferta['fecha'] ?></date></p>
                <p><b>Enlace:</b> <a href="<?= $oferta['url'] ?>" target="_blank"><?= $oferta['url'] ?></a></p>
                <p><b>Descripción:</b> <?= $oferta['descripcion'] ?></p>
                <p><b>Requisitos:</b> <?= $oferta['requisitos'] ?></p>
                <p><b>Salario:</b> <?= $oferta['salario'] ?></p>
                <p><b>Empresa:</b> <?= $oferta['empresa'] ?></p>
                <footer>
                    <a href="/Oferta/editar/<?= $oferta['id'] ?>">Editar</a>
                    <a href="/Oferta/borrar/<?= $oferta['id'] ?>">Borrar</a>
                </footer>
            </article>
        <?php endforeach ?>
    <?php endif ?>
</div>