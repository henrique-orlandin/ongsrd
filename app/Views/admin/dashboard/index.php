<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $latestMessages = $latestMessages ?? []; ?>

<div class="cards">
    <div class="card"><h3><i class="fa-solid fa-dog"></i>Pets</h3><p><?= esc((string) ($counts['pets'] ?? 0)) ?></p></div>
    <div class="card"><h3><i class="fa-solid fa-heart"></i>Finais Felizes</h3><p><?= esc((string) ($counts['happy_endings'] ?? 0)) ?></p></div>
    <div class="card"><h3><i class="fa-solid fa-handshake"></i>Parceiros</h3><p><?= esc((string) ($counts['partners'] ?? 0)) ?></p></div>
</div>

<section class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-inbox"></i>Últimas Mensagens</h2>
        <a class="btn" href="<?= base_url('cms/contact-messages') ?>"><i class="fa-solid fa-list"></i>Ver todas</a>
    </div>
    <table class="table">
        <thead>
        <tr><th>Nome</th><th>E-mail</th><th>Status</th><th>Data</th></tr>
        </thead>
        <tbody>
        <?php foreach ($latestMessages as $row): ?>
            <tr>
                <td><?= esc((string) $row['name']) ?></td>
                <td><?= esc((string) $row['email']) ?></td>
                <td><span class="badge <?= (int) $row['is_new'] === 1 ? 'new' : '' ?>"><?= (int) $row['is_new'] === 1 ? 'Nova' : 'Lida' ?></span></td>
                <td><?= esc((string) $row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
