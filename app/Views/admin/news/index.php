<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<section class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-newspaper"></i>Noticias</h2>
        <a class="btn" href="<?= base_url('cms/noticias/new') ?>"><i class="fa-solid fa-plus"></i>Nova Noticia</a>
    </div>
    <table class="table">
        <thead><tr><th></th><th>Titulo</th><th>Publicacao</th><th class="actions">Acoes</th></tr></thead>
        <tbody>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?php if (! empty($row['main_image'])): ?><img src="<?= base_url((string) $row['main_image']) ?>" class="thumb" alt=""><?php endif; ?></td>
                <td><?= esc((string) $row['title']) ?></td>
                <td>
                    <?php if (! empty($row['published_at'])): ?>
                        <?= esc(date('d/m/Y H:i', strtotime((string) $row['published_at']))) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a class="link" href="<?= base_url('cms/noticias/edit/' . $row['id']) ?>"><i class="fa-solid fa-pen"></i>Editar</a>
                    <form method="post" action="<?= base_url('cms/noticias/delete/' . $row['id']) ?>" class="inline" onsubmit="return confirm('Excluir esta noticia?');">
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
