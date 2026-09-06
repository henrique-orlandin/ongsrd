<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/how-to-help.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$howToHelp = $howToHelp ?? [];
$title = trim((string) ($howToHelp['title'] ?? ''));
$description = trim((string) ($howToHelp['description'] ?? ''));
$image = trim((string) ($howToHelp['image'] ?? ''));
$imageMobile = trim((string) ($howToHelp['image_mobile'] ?? '')) ?: $image;

$freedoms = [
    ['icon' => 'fa-bowl-food', 'title' => 'Livre de fome e sede', 'text' => 'Acesso a água limpa e alimentação adequada.'],
    ['icon' => 'fa-house-chimney', 'title' => 'Livre de desconforto', 'text' => 'Abrigo seguro e ambiente apropriado.'],
    ['icon' => 'fa-heart-pulse', 'title' => 'Livre de dor, ferimentos e doenças', 'text' => 'Prevenção, diagnóstico e tratamento quando necessário.'],
    ['icon' => 'fa-paw', 'title' => 'Livre para expressar seu comportamento natural', 'text' => 'Espaço, estímulo e companhia.'],
    ['icon' => 'fa-shield-heart', 'title' => 'Livre de medo e estresse', 'text' => 'Vida em condições que promovam bem-estar emocional.'],
];
?>

<section class="help-page" id="como-ajudar">
    <div class="container">
        <header class="section-header help-header">
            <div>
                <p class="section-tag">Participe</p>
                <h2>Como Ajudar</h2>
            </div>
        </header>

        <div class="help-intro">
            <figure class="help-figure">
                <?php if ($image !== ''): ?>
                    <picture>
                        <?php if ($imageMobile !== ''): ?>
                            <source media="(max-width: 768px)" srcset="<?= esc(base_url($imageMobile), 'attr') ?>">
                        <?php endif; ?>
                        <img src="<?= esc(base_url($image), 'attr') ?>" alt="Como ajudar a ONG SRD" loading="lazy">
                    </picture>
                <?php else: ?>
                    <div class="img-placeholder" aria-hidden="true"></div>
                <?php endif; ?>
            </figure>

            <div class="help-copy">
                <?php if ($title !== ''): ?>
                    <h3><?= esc($title) ?></h3>
                <?php endif; ?>

                <?php if ($description !== ''): ?>
                    <p class="lead"><?= nl2br(esc($description)) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="mistreatment-section">
    <div class="container">
        <h3>O que são maus-tratos aos animais?</h3>
        <p>Maus-tratos não são apenas agressões físicas. Significa qualquer ato que cause dor, sofrimento ou afete o bem-estar de um animal.</p>
        <ul class="mistreatment-list">
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Espancar, ferir ou envenenar.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Negligenciar alimentação ou água.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Manter preso sem espaço adequado ou sob sol e chuva.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Abandonar em vias públicas.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Negar tratamento veterinário.</li>
        </ul>
    </div>
</section>

<section class="freedoms-section">
    <div class="container">
        <p class="freedoms-lede">A base de tudo o que fazemos está nas <strong>Cinco Liberdades</strong>, um conceito internacional que define o mínimo que todo animal precisa para viver com dignidade:</p>

        <div class="freedoms-grid">
            <?php foreach ($freedoms as $freedom): ?>
                <article class="freedom-card">
                    <div class="freedom-icon" aria-hidden="true">
                        <i class="fa-solid <?= esc($freedom['icon']) ?>"></i>
                    </div>
                    <h4><?= esc($freedom['title']) ?></h4>
                    <p><?= esc($freedom['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="actions help-actions">
            <a class="btn-primary" data-text="Fazer uma Denúncia" href="<?= base_url('contato') ?>">Fazer uma Denúncia</a>
            <a class="link" href="<?= base_url('adotar') ?>">Conheça os pets para adoção</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
