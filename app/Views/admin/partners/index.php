<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<section class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-handshake"></i>Parceiros</h2>
        <a class="btn" href="<?= base_url('cms/partners/new') ?>"><i class="fa-solid fa-plus"></i>Novo Parceiro</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Logo</th>
                <th>Link</th>
                <th>Ordem</th>
                <th>Status</th>
                <th class="actions">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $row): ?>
                <tr>
                    <td><?= esc((string) $row['name']) ?></td>
                    <td><?php if (! empty($row['image'])): ?><img src="<?= base_url((string) ($row['image_thumb'] ?: $row['image'])) ?>" class="thumb" alt=""><?php endif; ?></td>
                    <td>
                        <?php if (! empty($row['link_url'])): ?>
                            <a class="link" href="<?= esc((string) $row['link_url'], 'attr') ?>" target="_blank" rel="noopener">Abrir</a>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc((string) ($row['sort_order'] ?? 0)) ?></td>
                    <td><?= (int) ($row['is_active'] ?? 0) === 1 ? 'Ativo' : 'Inativo' ?></td>
                    <td class="actions">
                        <a class="link" href="<?= base_url('cms/partners/edit/' . $row['id']) ?>"><i class="fa-solid fa-pen"></i>Editar</a>
                        <form method="post" action="<?= base_url('cms/partners/delete/' . $row['id']) ?>" class="inline" onsubmit="return confirm('Excluir este parceiro?');">
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