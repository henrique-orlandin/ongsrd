<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="<?= base_url('assets/site/maintenance.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $message = trim((string) ($message ?? '')); ?>

<section class="maintenance-page">
    <div class="container maintenance-box">
        <div class="maintenance-icon" aria-hidden="true"><i class="fa-solid fa-paw"></i></div>
        <p class="section-tag">Em manutenção</p>
        <h2>Já voltamos!</h2>
        <p class="maintenance-message">
            <?php if ($message !== ''): ?>
                <?= nl2br(esc($message)) ?>
            <?php else: ?>
                Estamos atualizando o site da ONG SRD. Voltamos em breve — obrigado pela paciência!
            <?php endif; ?>
        </p>
    </div>
</section>

<?= $this->endSection() ?>
