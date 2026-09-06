<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/about.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$about = $about ?? [];
$title = trim((string) ($about['title'] ?? ''));
$description = trim((string) ($about['description'] ?? ''));
$image = trim((string) ($about['image'] ?? ''));
$imageMobile = trim((string) ($about['image_mobile'] ?? '')) ?: $image;
?>

<section class="about-page" id="quem-somos">
    <div class="container">
        <header class="section-header about-header">
            <div>
                <p class="section-tag">Institucional</p>
                <h2>Quem Somos</h2>
            </div>
        </header>

        <div class="about-layout">
            <figure class="about-figure">
                <?php if ($image !== ''): ?>
                    <picture>
                        <?php if ($imageMobile !== ''): ?>
                            <source media="(max-width: 768px)" srcset="<?= esc(base_url($imageMobile), 'attr') ?>">
                        <?php endif; ?>
                        <img src="<?= esc(base_url($image), 'attr') ?>" alt="Equipe da ONG SRD" loading="lazy">
                    </picture>
                <?php else: ?>
                    <div class="img-placeholder" aria-hidden="true"></div>
                <?php endif; ?>
            </figure>

            <div class="about-copy">
                <?php if ($title !== ''): ?>
                    <h3><?= esc($title) ?></h3>
                <?php endif; ?>

                <?php if ($description !== ''): ?>
                    <p class="lead"><?= nl2br(esc($description)) ?></p>
                <?php else: ?>
                    <p class="lead empty-state">Em breve, mais informações sobre a nossa história.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
