<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/adopt.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$pet       = $pet       ?? [];
$images    = $images    ?? [];
$thumbnail = $thumbnail ?? null;

$genderLabel = ($pet['gender'] ?? '') === 'M' ? 'Macho' : 'Fêmea';
$ageLabel    = ((int)($pet['age'] ?? 0)) === 1 ? '1 ano' : ((int)($pet['age'] ?? 0)) . ' anos';
$sizeLabels  = ['P'=>'Pequeno','M'=>'Médio','G'=>'Grande','GG'=>'Extra grande'];
$sizeLabel   = $sizeLabels[$pet['size'] ?? ''] ?? ($pet['size'] ?? '');
?>

<div class="pet-detail-page">
    <div class="container">
        <nav class="breadcrumb" aria-label="Caminho">
            <a href="<?= base_url('/') ?>">Início</a>
            <span aria-hidden="true">›</span>
            <a href="<?= base_url('adotar') ?>">Adoção</a>
            <span aria-hidden="true">›</span>
            <span><?= esc((string) ($pet['name'] ?? '')) ?></span>
        </nav>

        <div class="pet-detail-layout">

            <!-- Gallery -->
            <div class="pet-detail-gallery">
                <div class="pet-detail-main-img">
                    <?php if ($thumbnail !== null): ?>
                        <img id="gallery-main" src="<?= base_url(esc($thumbnail, 'attr')) ?>" alt="<?= esc((string) ($pet['name'] ?? '')) ?>">
                    <?php else: ?>
                        <div class="img-placeholder" aria-hidden="true"></div>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 1): ?>
                    <div class="pet-detail-thumbs">
                        <?php foreach ($images as $img): ?>
                            <button type="button" class="thumb-btn <?= $img['image_path'] === $thumbnail ? 'is-active' : '' ?>"
                                    data-src="<?= base_url(esc((string) $img['image_path'], 'attr')) ?>">
                                <img src="<?= base_url(esc((string) $img['image_path'], 'attr')) ?>"
                                     alt="Foto de <?= esc((string) ($pet['name'] ?? '')) ?>" loading="lazy">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="pet-detail-info">
                <h1><?= esc((string) ($pet['name'] ?? '')) ?></h1>

                <ul class="pet-detail-attrs">
                    <li><i class="fa-solid fa-paw" aria-hidden="true"></i> <strong>Tipo:</strong> <?= esc((string) ($pet['type'] ?? '')) ?></li>
                    <li><i class="fa-solid fa-venus-mars" aria-hidden="true"></i> <strong>Sexo:</strong> <?= esc($genderLabel) ?></li>
                    <li><i class="fa-regular fa-calendar" aria-hidden="true"></i> <strong>Idade:</strong> <?= esc($ageLabel) ?></li>
                    <li><i class="fa-solid fa-ruler-horizontal" aria-hidden="true"></i> <strong>Porte:</strong> <?= esc($sizeLabel) ?></li>
                </ul>

                <div class="pet-detail-description">
                    <?= $pet['description'] ?? '' ?>
                </div>

                <a href="<?= base_url('contato') ?>" class="adopt-cta-btn">
                    <i class="fa-solid fa-heart" aria-hidden="true"></i> Tenho Interesse em Adotar
                </a>
                <br>
                <a href="<?= base_url('adotar') ?>" class="link back-link">← Ver outros pets</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const mainImg = document.getElementById('gallery-main');
    if (!mainImg) return;

    document.querySelectorAll('.thumb-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mainImg.src = btn.dataset.src;
            document.querySelectorAll('.thumb-btn').forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
        });
    });
}());
</script>
<?= $this->endSection() ?>
