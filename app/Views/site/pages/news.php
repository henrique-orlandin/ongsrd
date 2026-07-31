<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/news.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $news = $news ?? []; ?>
<section class="news-page" id="noticias">
    <div class="container">
        <header class="section-header news-header">
            <div>
                <p class="section-tag">Blog</p>
                <h2>Notícias</h2>
            </div>
        </header>

        <?php if ($news !== []): ?>
            <div class="news-grid">
                <?php foreach ($news as $article): ?>
                    <?php $slug = $article['slug'] ?? ''; ?>
                    <article class="news-card">
                        <?php $detailUrl = base_url($slug !== '' ? 'noticias/' . $slug : 'noticias'); ?>
                        <a href="<?= esc($detailUrl, 'attr') ?>" class="news-card-img-link" tabindex="-1" aria-hidden="true">
                            <?php if (! empty($article['main_image'])): ?>
                                <img src="<?= base_url((string) $article['main_image']) ?>" alt="<?= esc((string) $article['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="img-placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                        </a>
                        <div class="news-card-body">
                            <?php if (! empty($article['published_at'])): ?>
                                <time class="news-card-date" datetime="<?= esc((string) $article['published_at']) ?>">
                                    <?= esc(date('d/m/Y', strtotime((string) $article['published_at']))) ?>
                                </time>
                            <?php endif; ?>
                            <h3><a href="<?= esc($detailUrl, 'attr') ?>"><?= esc((string) $article['title']) ?></a></h3>
                            <p><?= esc(mb_strimwidth(strip_tags((string) $article['description']), 0, 140, '...')) ?></p>
                            <a href="<?= esc($detailUrl, 'attr') ?>" class="link">Ler mais →</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">Nenhuma notícia cadastrada ainda.</p>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>

