<?= $this->extend('site/layouts/main') ?>

<?= $this->section('content') ?>
<?php

?>

<section class="how-section" id="como-ajudar">
    <div class="container split how-grid">
        <figure class="about-image how-to-help-image">
            <?php if (! empty($howToHelp['image'])): ?>
                <img src="<?= base_url($howToHelp['image']) ?>" alt="Como ajudar a ONG SRD">
            <?php else: ?>
                <div class="img-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </figure>
        <article>
            <h2>O que são maus-tratos aos animais?</h2>
            <p>Maus-tratos não são apenas agressões físicas. Significa qualquer ato que cause dor, sofrimento ou afete o bem-estar de um animal.</p>
            <ul>
                <li>Espancar, ferir ou envenenar.</li>
                <li>Negligenciar alimentação ou água.</li>
                <li>Manter preso sem espaço adequado ou sob sol e chuva.</li>
                <li>Abandonar em vias públicas.</li>
                <li>Negar tratamento veterinário.</li>
            </ul>
        </article>
    </div>
</section>

<section class="freedom-section">
    <div class="container">
        <p>A base de tudo o que fazemos está nas <strong>Cinco Liberdades</strong>, um conceito internacional que define o mínimo que todo animal precisa para viver com dignidade:</p>
        <div class="freedom-grid">
            <article>
                <h4>Livre de fome e sede</h4>
                <p>Acesso a água limpa e alimentação adequada.</p>
            </article>
            <article>
                <h4>Livre de desconforto</h4>
                <p>Abrigo seguro e ambiente apropriado.</p>
            </article>
            <article>
                <h4>Livre de dor, ferimentos e doenças</h4>
                <p>Prevenção, diagnóstico e tratamento quando necessário.</p>
            </article>
            <article>
                <h4>Livre para expressar seu comportamento natural</h4>
                <p>Espaço, estímulo e companhia.</p>
            </article>
            <article>
                <h4>Livre de medo e estresse</h4>
                <p>Vida em condições que promovam bem-estar emocional.</p>
            </article>
        </div>
    </div>
</section>

<?= $this->endSection() ?>