<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/news.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$news      = $news      ?? [];
$images    = $images    ?? [];
$mainImage = $mainImage ?? null;
?>

<article class="news-detail-page">
    <div class="container news-detail-body">
        <nav class="breadcrumb" aria-label="Caminho">
            <a href="<?= base_url('/') ?>">Início</a>
            <span aria-hidden="true">›</span>
            <a href="<?= base_url('noticias') ?>">Notícias</a>
            <span aria-hidden="true">›</span>
            <span><?= esc((string) $news['title']) ?></span>
        </nav>
        
        <header class="news-detail-header">
            <?php if (! empty($news['published_at'])): ?>
                <time class="news-detail-date" datetime="<?= esc((string) $news['published_at']) ?>">
                    <?= esc(date('d \d\e F \d\e Y', strtotime((string) $news['published_at']))) ?>
                </time>
            <?php endif; ?>
            <h1><?= esc((string) $news['title']) ?></h1>
        </header>

        <?php if ($mainImage !== null): ?>
            <figure class="news-detail-hero">
                <picture> 
                    <img src="<?= base_url(esc($mainImage, 'attr')) ?>" alt="<?= esc((string) $news['title']) ?>" loading="eager">
                </picture>
            </figure>
        <?php endif; ?>
            
        <div class="news-detail-content">
            <?= $news['description'] ?>
        </div>

        <?php $galleryImages = array_filter($images, fn($img) => $img['image_path'] !== $mainImage); ?>
        <?php if (count($galleryImages) > 0): ?>
            <div class="news-detail-gallery">
                <h3>Galeria</h3>
                <div class="detail-gallery-grid">
                    <?php foreach ($galleryImages as $img): ?>
                        <figure>
                            <img src="<?= base_url(esc((string) $img['image_path'], 'attr')) ?>" alt="Foto da notícia" loading="lazy">
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="news-detail-back">
            <a href="<?= base_url('noticias') ?>" class="btn-primary" data-text="← Voltar para Notícias">← Voltar para Notícias</a>
        </div>
    </div>
</article>
<?= $this->endSection() ?>