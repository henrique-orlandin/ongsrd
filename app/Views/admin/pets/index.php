<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<section class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-dog"></i>Pets</h2>
        <a class="btn" href="<?= base_url('cms/pets/new') ?>"><i class="fa-solid fa-plus"></i>Novo Pet</a>
    </div>
    <table class="table">
        <thead><tr><th></th><th>Nome</th><th>Tipo</th><th>Idade</th><th>Porte</th><th>Sexo</th><th class="actions">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?php if ($row['thumbnail']): ?><img src="<?= base_url(esc($row['thumbnail'])) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:4px;"><?php endif; ?></td>
                <td><?= esc((string) $row['name']) ?></td>
                <td><?= esc((string) $row['type']) ?></td>
                <td><?= esc((string) $row['age']) ?></td>
                <td><?= esc((string) $row['size']) ?></td>
                <td><?= esc((string) $row['gender']) ?></td>
                <td class="actions">
                    <a class="link" href="<?= base_url('cms/pets/edit/' . $row['id']) ?>"><i class="fa-solid fa-pen"></i>Editar</a>
                    <form method="post" action="<?= base_url('cms/pets/delete/' . $row['id']) ?>" class="inline" onsubmit="return confirm('Excluir este pet?');">
                        <?= csrf_field() ?>
                        <button class="link danger" type="submit"><i class="fa-solid fa-trash"></i>Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?= $this->endSection() ?>
