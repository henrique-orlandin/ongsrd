<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$item = $item ?? null;
$itemGroups = $itemGroups ?? [];
$groups = $groups ?? [];
$isSelf = $isSelf ?? false;
$currentGroup = $itemGroups[0] ?? null;
?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-user"></i><?= $item ? 'Editar Usuário' : 'Novo Usuário' ?></h2>

    <form method="post" action="<?= $item ? base_url('cms/users/update/' . $item['id']) : base_url('cms/users/create') ?>">
        <?= csrf_field() ?>

        <label>Nome de usuário
            <input type="text" name="username" value="<?= esc(old('username', $item['username'] ?? '')) ?>" required>
        </label>

        <label>Email
            <input type="email" name="email" value="<?= esc(old('email', $item['email'] ?? '')) ?>" required>
        </label>

        <?php if ($isSelf): ?>
            <label>Grupo
                <input type="text" value="<?= esc($groups[$currentGroup] ?? $currentGroup ?? '-') ?>" disabled>
            </label>
            <small>Você não pode alterar seu próprio grupo. Peça a outro super administrador.</small>
        <?php else: ?>
            <label>Grupo
                <select name="group" required>
                    <option value="" disabled <?= $currentGroup === null ? 'selected' : '' ?>>Selecione</option>
                    <?php foreach ($groups as $key => $title): ?>
                        <option value="<?= esc($key) ?>" <?= old('group', $currentGroup) === $key ? 'selected' : '' ?>><?= esc($title) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>

        <?php if ($item): ?>
            <?php if ($isSelf): ?>
                <label class="checkbox-inline">
                    <input type="checkbox" checked disabled> Ativo
                </label>
                <small>Você não pode desativar sua própria conta.</small>
            <?php else: ?>
                <label class="checkbox-inline">
                    <input type="checkbox" name="active" value="1" <?= (int) old('active', $item['active'] ?? 1) === 1 ? 'checked' : '' ?>> Ativo
                </label>
            <?php endif; ?>
        <?php endif; ?>

        <label><?= $item ? 'Nova senha (deixe em branco para manter a atual)' : 'Senha' ?>
            <input type="password" name="password" autocomplete="new-password" <?= $item ? '' : 'required' ?>>
        </label>

        <label>Confirmar senha
            <input type="password" name="password_confirm" autocomplete="new-password" <?= $item ? '' : 'required' ?>>
        </label>

        <div class="actions"><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button></div>
    </form>
</section>
<?= $this->endSection() ?>
