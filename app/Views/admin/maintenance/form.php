<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $item = $item ?? null; ?>
<?php $isEnabled = (int) old('is_enabled', $item['is_enabled'] ?? 0) === 1; ?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-triangle-exclamation"></i>Modo de Manutenção</h2>
    <p class="panel-hint">
        Enquanto ativo, visitantes anônimos veem uma página de manutenção (sem indexação por buscadores) em vez do
        site. Quem estiver logado no CMS continua vendo o site normalmente, para revisar o conteúdo antes de publicar.
    </p>

    <form method="post" action="<?= base_url('cms/settings/maintenance') ?>">
        <?= csrf_field() ?>

        <label class="switch-field">
            <span class="switch">
                <input type="checkbox" name="is_enabled" value="1" <?= $isEnabled ? 'checked' : '' ?>>
                <span class="switch-track" aria-hidden="true"></span>
            </span>
            <span class="switch-label">Ativar modo de manutenção</span>
        </label>

        <label>Mensagem exibida aos visitantes (opcional)
            <textarea name="message" rows="10" maxlength="500" placeholder="Ex: Estamos atualizando o site. Voltamos em breve!"><?= esc(old('message', $item['message'] ?? '')) ?></textarea>
        </label>

        <div class="actions"><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button></div>
    </form>
</section>
<?= $this->endSection() ?>
