<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $item = $item ?? []; ?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-envelope"></i>Detalhes da Mensagem</h2>
    <div class="detail-list">
        <div><strong>Nome:</strong> <?= esc((string) ($item['name'] ?? '')) ?></div>
        <div><strong>E-mail:</strong> <?= esc((string) ($item['email'] ?? '')) ?></div>
        <div><strong>Telefone:</strong> <?= esc((string) ($item['phone'] ?? '-')) ?></div>
        <div><strong>Status:</strong> <?= (int) $item['is_new'] === 1 ? 'Nova' : 'Lida' ?></div>
        <div><strong>Mensagem:</strong><p><?= nl2br(esc((string) ($item['message'] ?? ''))) ?></p></div>
    </div>
    <a class="btn" href="<?= base_url('cms/contact-messages') ?>"><i class="fa-solid fa-arrow-left"></i>Voltar</a>
</section>
<?= $this->endSection() ?>
