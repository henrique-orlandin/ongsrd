<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'CMS') ?></title>
    <!-- icon -->
    <link rel="icon" href="<?= base_url('assets/icon.png') ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/cms.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php $unreadMessageCount = (int) ($unreadMessageCount ?? 0); ?>
    <?php $flashMessage = session('message'); ?>
    <?php $flashError = session('error'); ?>
    <div class="cms-shell">
        <aside class="sidebar">
            <div class="brand">
                <a href="<?= base_url('cms/dashboard') ?>">
                    <img src="<?= base_url('assets/logo.png') ?>" alt="ONG SRD Logo">
                </a>
            </div>
            <nav class="menu">
                <a href="<?= base_url('cms/dashboard') ?>" class="<?= url_is('cms/dashboard') ? 'active' : '' ?>"><i class="fa-solid fa-gauge"></i> Painel</a>
                <a href="<?= base_url('cms/about') ?>" class="<?= url_is('cms/about*') ? 'active' : '' ?>"><i class="fa-solid fa-circle-info"></i> Sobre</a>
                <a href="<?= base_url('cms/pets') ?>" class="<?= url_is('cms/pets*') ? 'active' : '' ?>"><i class="fa-solid fa-dog"></i> Pets</a>
                <a href="<?= base_url('cms/how-to-help') ?>" class="<?= url_is('cms/how-to-help*') ? 'active' : '' ?>"><i class="fa-solid fa-hand-holding-heart"></i> Como Ajudar</a>
                <a href="<?= base_url('cms/happy-endings') ?>" class="<?= url_is('cms/happy-endings*') ? 'active' : '' ?>"><i class="fa-solid fa-heart"></i> Finais Felizes</a>
                <a href="<?= base_url('cms/home-page') ?>" class="<?= url_is('cms/home-page*') ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Página Inicial</a>
                <a href="<?= base_url('cms/banners') ?>" class="<?= url_is('cms/banners*') ? 'active' : '' ?>"><i class="fa-solid fa-images"></i> Banners</a>
                <a href="<?= base_url('cms/noticias') ?>" class="<?= url_is('cms/noticias*') ? 'active' : '' ?>"><i class="fa-solid fa-newspaper"></i> Notícias</a>
                <a href="<?= base_url('cms/partners') ?>" class="<?= url_is('cms/partners*') ? 'active' : '' ?>"><i class="fa-solid fa-handshake"></i> Parceiros</a>
                <a href="<?= base_url('cms/contact-messages') ?>" class="<?= url_is('cms/contact-messages*') ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Mensagens</a>
            </nav>
        </aside>

        <main class="main">
            <header class="topbar">
                <h1><?= esc($title ?? 'CMS') ?></h1>
                <div class="topbar-right">
                    <a class="bell" href="<?= base_url('cms/contact-messages') ?>">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Mensagens</span>
                        <?php if ($unreadMessageCount > 0): ?>
                            <em><?= esc((string) $unreadMessageCount) ?></em>
                        <?php endif; ?>
                    </a>
                    <div class="user-chip">
                        <i class="fa-solid fa-user"></i>
                        <?= esc(auth()->user()->username ?? auth()->user()->email ?? 'Usuario') ?>
                    </div>
                    <a class="logout" href="<?= base_url('cms/logout') ?>"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
                </div>
            </header>

            <?php if (is_string($flashMessage) && $flashMessage !== ''): ?>
                <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?= esc($flashMessage) ?></div>
            <?php endif; ?>

            <?php if (is_string($flashError) && $flashError !== ''): ?>
                <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?= esc($flashError) ?></div>
            <?php endif; ?>

            <?php if (session()->has('errors')): ?>
                <div class="alert error">
                    <?php foreach (session('errors') as $error): ?>
                        <div><i class="fa-solid fa-triangle-exclamation"></i> <?= esc((string) $error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <script>
        (function() {
            if (typeof Quill === 'undefined') {
                return;
            }

            const textareas = Array.from(document.querySelectorAll('.editor'));

            if (textareas.length === 0) {
                return;
            }

            const editors = [];
            const Delta = Quill.import('delta');

            const resizeImageToMax = function(file, maxDimension) {
                return new Promise(function(resolve, reject) {
                    const reader = new FileReader();

                    reader.onerror = function() {
                        reject(new Error('Falha ao ler a imagem.'));
                    };

                    reader.onload = function(e) {
                        const img = new Image();
                        img.onerror = function() {
                            reject(new Error('Falha ao processar a imagem.'));
                        };

                        img.onload = function() {
                            let width = img.width;
                            let height = img.height;

                            if (width > maxDimension || height > maxDimension) {
                                const ratio = Math.min(maxDimension / width, maxDimension / height);
                                width = Math.round(width * ratio);
                                height = Math.round(height * ratio);
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;

                            const context = canvas.getContext('2d');
                            if (!context) {
                                reject(new Error('Falha ao redimensionar a imagem.'));
                                return;
                            }

                            context.drawImage(img, 0, 0, width, height);

                            const mimeType = file.type && file.type.startsWith('image/') ? file.type : 'image/jpeg';
                            resolve({
                                dataUrl: canvas.toDataURL(mimeType, 0.92),
                                width: width,
                                height: height
                            });
                        };

                        img.src = e.target && typeof e.target.result === 'string' ? e.target.result : '';
                    };

                    reader.readAsDataURL(file);
                });
            };

            const askImageWidth = function(currentWidth) {
                const raw = prompt('Informe a nova largura da imagem em px (maximo 900):', String(currentWidth));
                if (raw === null) {
                    return null;
                }

                const parsed = Number(raw.trim());
                if (!Number.isFinite(parsed)) {
                    alert('Informe um valor numerico valido.');
                    return null;
                }

                return Math.max(40, Math.min(900, Math.round(parsed)));
            };

            textareas.forEach(function(textarea, index) {
                const wrapper = document.createElement('div');
                wrapper.className = 'quill-field';
                wrapper.id = 'quill-editor-' + index;
                const wrappingLabel = textarea.closest('label');

                if (wrappingLabel && wrappingLabel.classList.contains('editor-field')) {
                    wrapper.classList.add('editor-field');
                }

                textarea.style.display = 'none';
                if (wrappingLabel && wrappingLabel.contains(textarea)) {
                    wrappingLabel.insertAdjacentElement('afterend', wrapper);
                } else {
                    textarea.insertAdjacentElement('afterend', wrapper);
                }

                const quill = new Quill(wrapper, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ header: [2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });

                let selectedImage = null;

                const toolbar = quill.getModule('toolbar');
                const toolbarContainer = toolbar && toolbar.container ? toolbar.container : null;

                if (toolbarContainer) {
                    toolbarContainer.querySelectorAll('button').forEach(function(button) {
                        button.type = 'button';
                    });
                }

                const initialValue = textarea.value ? textarea.value.trim() : '';
                if (initialValue !== '') {
                    quill.clipboard.dangerouslyPasteHTML(initialValue);
                }

                quill.clipboard.addMatcher(Node.ELEMENT_NODE, function(node, delta) {
                    const lines = [];

                    delta.ops.forEach(function(op) {
                        if (typeof op.insert !== 'string') {
                            return;
                        }

                        lines.push({ insert: op.insert });
                    });

                    return new Delta(lines);
                });

                quill.root.addEventListener('click', function(event) {
                    if (event.target instanceof HTMLImageElement) {
                        selectedImage = event.target;
                        return;
                    }

                    selectedImage = null;
                });

                quill.root.addEventListener('dblclick', function(event) {
                    if (!(event.target instanceof HTMLImageElement)) {
                        return;
                    }

                    selectedImage = event.target;
                    const currentWidth = selectedImage.clientWidth || selectedImage.naturalWidth || 300;
                    const nextWidth = askImageWidth(currentWidth);

                    if (nextWidth === null) {
                        return;
                    }

                    selectedImage.style.width = nextWidth + 'px';
                    selectedImage.style.maxWidth = '100%';
                    selectedImage.style.height = 'auto';
                    quill.update('user');
                });

                toolbar.addHandler('image', function() {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/jpeg,image/png,image/webp,image/gif';

                    input.onchange = function() {
                        const file = input.files && input.files[0] ? input.files[0] : null;
                        if (!file) {
                            return;
                        }

                        const maxSize = 1024 * 1024;
                        if (file.size > maxSize) {
                            alert('A imagem deve ter no maximo 1 MB.');
                            return;
                        }

                        resizeImageToMax(file, 900)
                            .then(function(result) {
                                const range = quill.getSelection(true);
                                const indexToInsert = range ? range.index : quill.getLength();

                                quill.insertEmbed(indexToInsert, 'image', result.dataUrl, 'user');
                                quill.setSelection(indexToInsert + 1, 0, 'silent');

                                const editorImages = quill.root.querySelectorAll('img');
                                const inserted = editorImages.length > 0 ? editorImages[editorImages.length - 1] : null;
                                if (inserted instanceof HTMLImageElement) {
                                    inserted.style.width = result.width + 'px';
                                    inserted.style.maxWidth = '100%';
                                    inserted.style.height = 'auto';
                                    selectedImage = inserted;
                                }
                            })
                            .catch(function(error) {
                                alert(error && error.message ? error.message : 'Nao foi possivel inserir a imagem.');
                            });
                    };

                    input.click();
                });

                const syncValue = function() {
                    const html = quill.root.innerHTML.trim();
                    textarea.value = html === '<p><br></p>' ? '' : html;
                };

                quill.on('text-change', syncValue);
                syncValue();
                editors.push({ textarea: textarea, sync: syncValue });
            });

            document.addEventListener('submit', function(event) {
                const submitter = event && event.submitter ? event.submitter : null;
                if (submitter instanceof HTMLElement && submitter.closest('.ql-toolbar')) {
                    event.preventDefault();
                    return;
                }

                editors.forEach(function(entry) {
                    entry.sync();
                });
            }, true);
        })();
    </script>
</body>

</html>