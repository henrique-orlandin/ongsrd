<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<section class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-heart"></i>Finais Felizes</h2>
        <a class="btn" href="<?= base_url('cms/happy-endings/new') ?>"><i class="fa-solid fa-plus"></i>Nova História</a>
    </div>
    <table class="table">
        <thead><tr><th>Título</th><th>Imagem</th><th class="actions">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= esc((string) $row['title']) ?></td>
                <td><?php if (! empty($row['image'])): ?><img src="<?= base_url($row['image']) ?>" class="thumb" alt=""><?php endif; ?></td>
                <td class="actions">
                    <a class="link" href="<?= base_url('cms/happy-endings/edit/' . $row['id']) ?>"><i class="fa-solid fa-pen"></i>Editar</a>
                    <form method="post" action="<?= base_url('cms/happy-endings/delete/' . $row['id']) ?>" class="inline" onsubmit="return confirm('Excluir esta história?');">
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
