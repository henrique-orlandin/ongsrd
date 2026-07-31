<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<section class="panel">
    <div class="panel-head"><h2><i class="fa-solid fa-envelope"></i>Mensagens</h2></div>
    <table class="table">
        <thead><tr><th>Nome</th><th>E-mail</th><th>Status</th><th class="actions">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= esc((string) $row['name']) ?></td>
                <td><?= esc((string) $row['email']) ?></td>
                <td><span class="badge <?= (int) $row['is_new'] === 1 ? 'new' : '' ?>"><?= (int) $row['is_new'] === 1 ? 'Nova' : 'Lida' ?></span></td>
                <td class="actions"><a class="link" href="<?= base_url('cms/contact-messages/' . $row['id']) ?>"><i class="fa-solid fa-up-right-from-square"></i>Abrir</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?= $this->endSection() ?>
