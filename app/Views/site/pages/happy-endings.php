<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/happy-endings.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$happyEndings = $happyEndings ?? [];
?>

<section class="happy-page" id="finais-felizes">
    <div class="container">
        <header class="section-header happy-header">
            <div>
                <p class="section-tag">Histórias reais</p>
                <h2>Finais Felizes</h2>
            </div>
        </header>

        <div class="happy-list">
            <?php if ($happyEndings !== []): ?>
                <?php foreach ($happyEndings as $item): ?>
                    <?php
                    $image = trim((string) ($item['image'] ?? ''));
                    $imageMobile = trim((string) ($item['image_mobile'] ?? '')) ?: $image;
                    ?>
                    <article class="happy-story">
                        <figure class="happy-figure">
                            <?php if ($image !== ''): ?>
                                <picture>
                                    <?php if ($imageMobile !== ''): ?>
                                        <source media="(max-width: 768px)" srcset="<?= esc(base_url($imageMobile), 'attr') ?>">
                                    <?php endif; ?>
                                    <img src="<?= esc(base_url($image), 'attr') ?>" alt="<?= esc((string) $item['title']) ?>" loading="lazy">
                                </picture>
                            <?php else: ?>
                                <div class="img-placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                        </figure>
                        <div class="happy-copy">
                            <h3><?= esc((string) $item['title']) ?></h3>
                            <p><?= esc((string) $item['description']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Nenhuma história cadastrada ainda.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
