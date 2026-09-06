<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/home.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$homeSettings = $homeSettings ?? [];
$news = $news ?? [];
$partners = $partners ?? [];
$banners = $banners ?? [];

$heroBanners = [];
$rawHeroImages = trim((string) ($homeSettings['image'] ?? ''));

foreach ($banners as $banner) {
    if (empty($banner['image'])) {
        continue;
    }

    $heroBanners[] = [
        'image' => base_url((string) $banner['image']),
        'link' => trim((string) ($banner['link_url'] ?? '')) ?: null,
        'title' => trim((string) ($banner['title'] ?? '')),
    ];
}

if ($heroBanners === []) {
    if ($rawHeroImages !== '') {
        $heroImagePaths = preg_split('/[\r\n,;]+/', $rawHeroImages) ?: [];
        foreach ($heroImagePaths as $path) {
            $cleanPath = trim((string) $path);
            if ($cleanPath !== '') {
                $heroBanners[] = [
                    'image' => base_url($cleanPath),
                    'link' => null,
                    'title' => '',
                ];
            }
        }
    }
}

if ($heroBanners === []) {
    $heroBanners = [[
        'image' => base_url('assets/site/home-banner.jpg'),
        'link' => null,
        'title' => '',
    ]];
}

$uniqueHeroBanners = [];
$seenHeroImages = [];
foreach ($heroBanners as $banner) {
    $image = (string) ($banner['image'] ?? '');
    if ($image === '' || in_array($image, $seenHeroImages, true)) {
        continue;
    }

    $seenHeroImages[] = $image;
    $uniqueHeroBanners[] = $banner;
}

$heroBanners = $uniqueHeroBanners;
$aboutImage = $heroBanners[0]['image'] ?? null;

$partnerSlides = [];
foreach ($partners as $partner) {
    $partnerName = (string) ($partner['name'] ?? 'Parceiro');
    $rawImages = trim((string) ($partner['image'] ?? ''));
    $imagePaths = $rawImages !== '' ? (preg_split('/[\r\n,;]+/', $rawImages) ?: []) : [];

    if ($imagePaths !== []) {
        foreach ($imagePaths as $imagePath) {
            $cleanImagePath = trim((string) $imagePath);
            if ($cleanImagePath !== '') {
                $partnerSlides[] = [
                    'name' => $partnerName,
                    'image' => base_url($cleanImagePath),
                    'link' => trim((string) ($partner['link_url'] ?? '')),
                ];
            }
        }
        continue;
    }

    $partnerSlides[] = [
        'name' => $partnerName,
        'image' => null,
        'link' => trim((string) ($partner['link_url'] ?? '')),
    ];
}

$truncate = static function (string $text, int $max): string {
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $max, '...');
    }

    return strlen($text) > $max ? substr($text, 0, $max) . '...' : $text;
};
?>

<section class="hero-section">
    <div class="carousel hero-carousel" data-carousel data-autoplay="true" data-interval="6000">
        <div class="carousel-track-container">
            <div class="carousel-track" style="--per-view:1;">
                <?php foreach ($heroBanners as $index => $banner): ?>
                    <?php $bannerLink = trim((string) ($banner['link'] ?? '')); ?>
                    <?php $isFirstBanner = (int) $index === 0; ?>
                    <div class="carousel-slide hero-slide">
                        <?php if ($bannerLink !== ''): ?>
                            <a class="hero-slide-link" href="<?= esc($bannerLink, 'attr') ?>" aria-label="Abrir banner">
                        <?php endif; ?>

                        <img
                            src="<?= esc((string) ($banner['image'] ?? ''), 'attr') ?>"
                            alt="<?= esc((string) ($banner['title'] ?? 'Banner ONG SRD')) ?>"
                            loading="<?= $isFirstBanner ? 'eager' : 'lazy' ?>"
                            fetchpriority="<?= $isFirstBanner ? 'high' : 'low' ?>"
                            decoding="async"
                            sizes="100vw">

                        <?php if ($bannerLink !== ''): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button type="button" class="carousel-control prev" data-carousel-prev aria-label="Banner anterior">
            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
        </button>
        <button type="button" class="carousel-control next" data-carousel-next aria-label="Próximo banner">
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        </button>
        <div class="carousel-dots" data-carousel-dots aria-label="Navegação do banner"></div>
    </div>
</section>

<section class="about-section" id="quem-somos">
    <div class="container split">
        <article>
            <p class="section-tag">Quem Somos</p>
            <h2>ONG SRD</h2>
            <p>
                Nascemos do inconformismo de pessoas que se recusaram a fechar os olhos para o sofrimento animal e doaram seu tempo para fazer o bem.
                Atualmente somos uma organização que atua diariamente em averiguação de denúncias de crimes de maus-tratos.
            </p>
            <p>Nossa missão é simples: lutar por justiça para quem não tem voz.</p>
        </article>
        <figure class="about-image">
            <?php if (! empty($aboutImage)): ?>
                <img src="<?= esc((string) $aboutImage, 'attr') ?>" alt="Equipe ONG SRD">
            <?php else: ?>
                <div class="img-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </figure>
    </div>
</section>

<section class="numbers-section">
    <div class="container">
        <h2>Nossos números falam por nós!</h2>
        <div class="numbers-grid">
            <article><strong data-count="2477">+0</strong><span>Averiguações de denúncias</span></article>
            <article><strong data-count="637">+0</strong><span>Resgates realizados</span></article>
            <article><strong data-count="534">+0</strong><span>Adoções responsáveis concluídas</span></article>
            <article><strong data-count="89">+0</strong><span>Processos em execução contra tutores</span></article>
            <article><strong data-count="13">+0</strong><span>Prisões em flagrantes</span></article>
        </div>
    </div>
</section>

<section class="menu-section" id="cards-menu">
    <?php
    $menuCards = [
        [
            'title' => 'Doe',
            'description' => 'Contribua com recursos, ração ou suporte para ampliar nossos resgates e atendimentos.',
            'icon' => 'fa-hand-holding-heart',
            'link' => base_url('contato'),
            'link_label' => 'Ir para Contato',
        ],
        [
            'title' => 'Denuncie',
            'description' => 'Saiba como reunir provas e encaminhar denúncias de maus-tratos com segurança.',
            'icon' => 'fa-bullhorn',
            'link' => base_url('como-ajudar'),
            'link_label' => 'Ver Orientações',
        ],
        [
            'title' => 'Adote',
            'description' => 'Conheça os pets que aguardam um lar e encontre o companheiro ideal para sua família.',
            'icon' => 'fa-paw',
            'link' => base_url('adotar'),
            'link_label' => 'Ver Mais',
        ],
    ];
    ?>
    <div class="container">
        <div class="action-menu-grid">
            <?php foreach ($menuCards as $card): ?>
                <article class="action-card">
                    <div class="action-card-icon" aria-hidden="true">
                        <i class="fa-solid <?= esc($card['icon']) ?>"></i>
                    </div>
                    <h3><?= esc($card['title']) ?></h3>
                    <p><?= esc($card['description']) ?></p>
                    <?php if (! empty($card['link'])): ?>
                        <a href="<?= esc((string) $card['link'], 'attr') ?>" class="action-link"><?= esc((string) $card['link_label']) ?></a>
                    <?php else: ?>
                        <span class="action-link is-disabled">Em breve</span>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="pets-section" id="ultimas-noticias">
    <div class="container">
        <div class="section-header">
            <h2>Últimas Notícias</h2>
        </div>
        <div class="cards-grid">
            <?php if ($news !== []): ?>
                <?php foreach ($news as $article): ?>
                    <article class="card" data-article-id="<?= esc((string) $article['id'], 'attr') ?>">
                        <?php if (! empty($article['main_image'])): ?>
                            <img src="<?= base_url((string) $article['main_image']) ?>" alt="<?= esc((string) $article['title']) ?>">
                        <?php else: ?>
                            <div class="img-placeholder" aria-hidden="true"></div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3><?= esc((string) $article['title']) ?></h3>
                            <p><?= esc($truncate((string) $article['description'], 140)) ?></p>
                            <a href="<?= base_url('noticias/' . ($article['slug'] ?? '')) ?>" class="link">Ler mais</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Nenhuma notícia cadastrada ainda.</p>
            <?php endif; ?>
        </div>
        <div class="actions"><a href="<?= base_url('noticias') ?>" class="btn btn-primary" data-text="Ver mais">Ver mais</a></div>
    </div>
</section>

<section class="partners-section">
    <div class="container">
        <h2>Parceiros</h2>
        <?php if ($partnerSlides !== []): ?>
            <div class="carousel partners-carousel" data-carousel data-autoplay="true" data-interval="3500" data-per-view-desktop="5" data-per-view-tablet="3" data-per-view-mobile="1">
                <div class="carousel-track-container">
                    <div class="carousel-track" style="--per-view:5;">
                        <?php foreach ($partnerSlides as $slide): ?>
                            <figure class="carousel-slide partner-slide">
                                <?php $partnerLink = trim((string) ($slide['link'] ?? '')); ?>
                                <?php if ($partnerLink !== ''): ?>
                                    <a href="<?= esc($partnerLink, 'attr') ?>" target="_blank" rel="noopener" aria-label="Abrir site do parceiro <?= esc((string) $slide['name']) ?>">
                                <?php endif; ?>

                                <?php if (! empty($slide['image'])): ?>
                                    <img src="<?= esc((string) $slide['image'], 'attr') ?>" alt="Parceiro <?= esc((string) $slide['name']) ?>">
                                <?php else: ?>
                                    <figcaption><?= esc((string) $slide['name']) ?></figcaption>
                                <?php endif; ?>

                                <?php if ($partnerLink !== ''): ?>
                                    </a>
                                <?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" class="carousel-control prev" data-carousel-prev aria-label="Parceiro anterior">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="carousel-control next" data-carousel-next aria-label="Próximo parceiro">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
                <div class="carousel-dots" data-carousel-dots aria-label="Navegação de parceiros"></div>
            </div>
        <?php else: ?>
            <p class="empty-state">Nenhum parceiro cadastrado ainda.</p>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/site/home.js') ?>" defer></script>
<?= $this->endSection() ?>