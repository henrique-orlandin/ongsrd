<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<section class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-users"></i>Usuários</h2>
        <a class="btn" href="<?= base_url('cms/users/new') ?>"><i class="fa-solid fa-plus"></i>Novo Usuário</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Email</th>
                <th>Grupo</th>
                <th>Status</th>
                <th>Último acesso</th>
                <th class="actions">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $row): ?>
                <tr>
                    <td><?= esc((string) $row['username']) ?><?= (int) $row['id'] === (int) auth()->id() ? ' <em>(você)</em>' : '' ?></td>
                    <td><?= esc((string) ($row['email'] ?? '-')) ?></td>
                    <td><?= $row['groups'] !== [] ? esc(implode(', ', $row['groups'])) : '-' ?></td>
                    <td><?= (int) $row['active'] === 1 ? 'Ativo' : 'Inativo' ?></td>
                    <td>
                        <?php if (! empty($row['last_active'])): ?>
                            <?= esc(date('d/m/Y H:i', strtotime((string) $row['last_active']))) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a class="link" href="<?= base_url('cms/users/edit/' . $row['id']) ?>"><i class="fa-solid fa-pen"></i>Editar</a>
                        <?php if ((int) $row['id'] !== (int) auth()->id()): ?>
                            <form method="post" action="<?= base_url('cms/users/delete/' . $row['id']) ?>" class="inline" onsubmit="return confirm('Excluir este usuário?');">
                                <?= csrf_field() ?>
                                <button class="link danger" type="submit"><i class="fa-solid fa-trash"></i>Excluir</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?= $this->endSection() ?>
