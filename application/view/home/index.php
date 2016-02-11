<?php $this->layout('layout') ?>
<?php if(Session::get('feedback_positive') || Session::get('feedback_negative')) : ?>
	<div class="container">
		<?php $this->insert('partials/feedback') ?>
	</div>
<?php endif; ?>
<div class="container">
    <h2><?= isset($seccion1) ? $seccion1: 'Introducción' ?></h2>

	<p>Esta aplicación es una manera de gestionar tus ofertas de trabajo, y las empresas que realizan estas ofertas, puesto que hoy en día las empresas como infojobs nos permiten suscribirnos a ofertas de trabajo, esta aplicación te facilita guardar dichas ofertas y la información relevante de la empresa.</p>
</div>
<?php $this->insert('partials/banner',['titulo' => $seccion2]) ?>
