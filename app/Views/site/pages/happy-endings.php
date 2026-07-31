<?= $this->extend('site/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$happyEndings = $happyEndings ?? [];
?>

<section class="happy-section" id="finais-felizes">
    <div class="container">
        <div class="section-header">
            <h2>Finais Felizes</h2>
        </div>
        <div class="happy-endings-container">
            <?php if ($happyEndings !== []): ?>
                <?php foreach ($happyEndings as $item): ?>
                    <article>
                        <div class="text">
                            <h3><?= esc($item['title']) ?></h3>
                            <p><?= esc($item['description']) ?></p>
                        </div>
                        <figure>
                            <?php if (! empty($item['image'])): ?>
                                <img src="<?= base_url($item['image']) ?>" alt="<?= esc($item['title']) ?>">
                            <?php else: ?>
                                <div class="img-placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                        </figure>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Nenhuma história cadastrada ainda.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>