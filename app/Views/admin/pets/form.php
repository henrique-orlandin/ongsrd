<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $item = $item ?? null; ?>
<?php $images = $images ?? []; ?>
<?php $existingImageCount = is_array($images) ? count($images) : 0; ?>
<?php $remainingSlots = max(0, 10 - $existingImageCount); ?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-dog"></i><?= $item ? 'Editar Pet' : 'Novo Pet' ?></h2>
    <form class="dropzone upload-image" method="post" enctype="multipart/form-data" action="<?= $item ? base_url('cms/pets/update/' . $item['id']) : base_url('cms/pets/create') ?>">
        <?= csrf_field() ?>
        <label>Nome <input type="text" name="name" value="<?= esc(old('name', $item['name'] ?? '')) ?>" required></label>
        <div class="form-row">
            <label>Tipo
                <select name="type" required>
                    <option value="Gato" <?= old('type', $item['type'] ?? '') === 'Gato' ? 'selected' : '' ?>>Gato</option>
                    <option value="Cachorro" <?= old('type', $item['type'] ?? '') === 'Cachorro' ? 'selected' : '' ?>>Cachorro</option>
                </select>
            </label>
            <label> Idade <input type="number" min="0" name="age" value="<?= esc((string) old('age', $item['age'] ?? 0)) ?>" required></label>
            <?php $currentAgeUnit = old('age_unit', $item['age_unit'] ?? 'years'); ?>
            <label>Unidade
                <select name="age_unit" required>
                    <option value="years" <?= $currentAgeUnit === 'years' ? 'selected' : '' ?>>Anos</option>
                    <option value="months" <?= $currentAgeUnit === 'months' ? 'selected' : '' ?>>Meses (filhote)</option>
                </select>
            </label>
        </div>
        <div class="form-row">
            <label>Porte
                <?php $currentSize = old('size', $item['size'] ?? ''); ?>
                <select name="size" required>
                    <option value="" <?= $currentSize === '' ? 'selected' : '' ?> disabled>Selecione</option>
                    <option value="P" <?= $currentSize === 'P' ? 'selected' : '' ?>>P</option>
                    <option value="M" <?= $currentSize === 'M' ? 'selected' : '' ?>>M</option>
                    <option value="G" <?= $currentSize === 'G' ? 'selected' : '' ?>>G</option>
                    <option value="GG" <?= $currentSize === 'GG' ? 'selected' : '' ?>>GG</option>
                </select>
            </label>
            <label>Sexo
                <select name="gender" required>
                    <option value="M" <?= old('gender', $item['gender'] ?? '') === 'M' ? 'selected' : '' ?>>Macho</option>
                    <option value="F" <?= old('gender', $item['gender'] ?? '') === 'F' ? 'selected' : '' ?>>Fêmea</option>
                </select>
            </label>
        </div>
        <label> Descrição <textarea name="description" rows="5" required><?= esc(old('description', $item['description'] ?? '')) ?></textarea></label>
        <label>Imagens da Galeria (max. 10 no total)
            <div id="pets-dropzone" class="upload-dropzone my-dropzone gallery-dropzone">
                <div class="dz-message"><i class="fa-solid fa-images"></i> Solte imagens do pet aqui ou clique para enviar.</div>
            </div>
            <?php if ($remainingSlots === 0): ?>
                <small>Todos os 10 espaços da galeria já estão em uso. Remova uma imagem para enviar outra.</small>
            <?php else: ?>
                <small><?= esc((string) $remainingSlots) ?> espaços de galeria disponíveis.</small>
            <?php endif; ?>
        </label>
        <?php if (! empty($images) && is_array($item) && isset($item['id'])): ?>
            <div class="gallery">
                <?php foreach ($images as $img): ?>
                    <figure class="<?= (int) $img['is_thumbnail'] === 1 ? 'is-thumbnail' : '' ?>">
                        <img src="<?= base_url($img['image_path']) ?>" alt="">
                        <div class="icons">
                        <?php if ((int) $img['is_thumbnail'] === 1): ?>
                            <span class="thumbnail-badge"><i class="fa-solid fa-star"></i></span>
                        <?php else: ?>
                            <button class="link" type="button"
                                data-thumbnail-url="<?= base_url('cms/pets/' . $item['id'] . '/images/' . $img['id'] . '/thumbnail') ?>"
                                data-csrf-name="<?= csrf_token() ?>"
                                data-csrf-value="<?= csrf_hash() ?>">
                                <i class="fa-regular fa-star"></i>
                            </button>
                        <?php endif; ?>
                        <button class="link danger" type="button"
                            data-delete-url="<?= base_url('cms/pets/' . $item['id'] . '/images/' . $img['id'] . '/delete') ?>"
                            data-csrf-name="<?= csrf_token() ?>"
                            data-csrf-value="<?= csrf_hash() ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        </div>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="actions"><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button></div>
    </form>
</section>
<script>
    document.querySelectorAll('button[data-thumbnail-url]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = btn.dataset.thumbnailUrl;
            const csrfName = btn.dataset.csrfName;
            const csrfValue = btn.dataset.csrfValue;
            const body = new URLSearchParams();
            body.append(csrfName, csrfValue);
            fetch(url, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(res) {
                    if (res.ok) { window.location.reload(); }
                    else { alert('Falha ao definir miniatura.'); }
                })
                .catch(function() { alert('Falha ao definir miniatura.'); });
        });
    });

    document.querySelectorAll('button[data-delete-url]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Remover esta imagem?')) return;
            const url = btn.dataset.deleteUrl;
            const csrfName = btn.dataset.csrfName;
            const csrfValue = btn.dataset.csrfValue;
            const body = new URLSearchParams();
            body.append(csrfName, csrfValue);
            fetch(url, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(res) {
                    if (res.ok || res.redirected) { window.location.reload(); }
                    else { alert('Falha ao remover imagem.'); }
                })
                .catch(function() { alert('Falha ao remover imagem.'); });
        });
    });

    Dropzone.autoDiscover = false;

    const dropzoneSelector = '#pets-dropzone';
    const form = document.querySelector(dropzoneSelector).closest('form');
    const maxFiles = <?= esc((string) $remainingSlots) ?>;
    let skipDropzoneSubmit = false;

    new Dropzone(dropzoneSelector, {
        url: form.getAttribute('action'),
        autoProcessQueue: false,
        maxFiles,
        acceptedFiles: 'image/*',
        paramName: 'images',
        uploadMultiple: true,
        parallelUploads: Math.max(1, maxFiles),
        addRemoveLinks: true,
        init: function() {
            form.addEventListener('submit', (e) => {
                if (skipDropzoneSubmit) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                if (this.getQueuedFiles().length <= 0) {
                    skipDropzoneSubmit = true;
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                    return;
                }

                this.processQueue();
            });

            this.on('maxfilesexceeded', function(file) {
                this.removeFile(file);
            });

            this.on('addedfile', function() {
                document.querySelector(dropzoneSelector).querySelector('.dz-message').style.display = 'none';
            });

            this.on('removedfile', function() {
                if (this.files.length === 0) {
                    document.querySelector(dropzoneSelector).querySelector('.dz-message').style.display = 'block';
                }
            });

            this.on('successmultiple', function(files, response) {
                if (response.success) {
                    window.location.href = "<?= base_url('cms/pets') ?>";
                    return;
                }

                alert('Erro ao enviar imagens: ' + (response.message || 'Erro desconhecido'));
            });

            this.on('success', function(file, response) {
                if (response.success) {
                    window.location.href = "<?= base_url('cms/pets') ?>";
                }
            });

            this.on('error', function(file, errorMessage, xhr) {
                const responseMessage = xhr && xhr.responseText ? xhr.responseText : errorMessage;
                alert('Erro ao enviar arquivo: ' + responseMessage);
            });

            this.on('sending', function(file, xhr, formData) {
                const data = new FormData(form);
                for (const pair of data.entries()) {
                    formData.append(pair[0], pair[1]);
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
