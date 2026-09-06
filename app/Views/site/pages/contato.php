<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/contact.css') ?>">
<?php if (! empty($recaptchaSiteKey ?? '')): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$success          = $success          ?? session()->getFlashdata('success');
$error            = $error            ?? session()->getFlashdata('error');
$recaptchaSiteKey = trim((string) ($recaptchaSiteKey ?? ''));
?>

<section class="contact-page" id="contato">
    <div class="container">
        <header class="section-header contact-header">
            <div>
                <p class="section-tag">Fale Conosco</p>
                <h2>Contato</h2>
            </div>
        </header>

        <div class="contact-layout">
            <!-- Form -->
            <div class="contact-form-col">
                <?php if ($success !== null): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <?= esc((string) $success) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error !== null): ?>
                    <div class="alert alert-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <?= esc((string) $error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('contato') ?>" novalidate>
                    <?= csrf_field() ?>

                    <!-- Honeypot – must stay empty -->
                    <div style="display:none;" aria-hidden="true">
                        <label>Deixe em branco <input type="text" name="website" autocomplete="off" tabindex="-1"></label>
                    </div>

                    <div class="form-group">
                        <label for="ct-name">Nome <span aria-hidden="true">*</span></label>
                        <input type="text" id="ct-name" name="name"
                               value="<?= esc((string) old('name', '')) ?>"
                               required maxlength="120" autocomplete="name"
                               placeholder="Seu nome completo">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ct-email">E-mail <span aria-hidden="true">*</span></label>
                            <input type="email" id="ct-email" name="email"
                                   value="<?= esc((string) old('email', '')) ?>"
                                   required maxlength="180" autocomplete="email"
                                   placeholder="seu@email.com">
                        </div>
                        <div class="form-group">
                            <label for="ct-phone">Telefone</label>
                            <input type="tel" id="ct-phone" name="phone"
                                   value="<?= esc((string) old('phone', '')) ?>"
                                   maxlength="30" autocomplete="tel"
                                   placeholder="(00) 00000-0000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ct-message">Mensagem <span aria-hidden="true">*</span></label>
                        <textarea id="ct-message" name="message" rows="6"
                                  required minlength="10" maxlength="2000"
                                  placeholder="Como podemos ajudar?"><?= esc((string) old('message', '')) ?></textarea>
                    </div>

                    <div class="form-group captcha-group">
                        <?php if ($recaptchaSiteKey !== ''): ?>
                            <div class="captcha-widget">
                                <div class="g-recaptcha" data-sitekey="<?= esc($recaptchaSiteKey) ?>"></div>
                            </div>
                        <?php else: ?>
                            <p class="captcha-config-warning">reCAPTCHA indisponível no momento. Tente novamente mais tarde.</p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Enviar mensagem
                    </button>
                </form>
            </div>

            <!-- Sidebar info -->
            <aside class="contact-info-col">
                <div class="contact-info-card">
                    <h3>ONG SRD</h3>
                    <p>Sem Raça Definida — proteção animal em Caxias do Sul.</p>

                    <ul class="contact-details">
                        <li>
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <address>Sarmento Leite, 3090<br>Caxias do Sul – RS, 95084-000</address>
                        </li>
                        <li>
                            <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                            <a href="https://www.instagram.com/ongsrdoficial/" target="_blank" rel="noopener">@ongsrdoficial</a>
                        </li>
                        <li>
                            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                            <a href="https://www.facebook.com/ongsemracadefinidacxs/" target="_blank" rel="noopener">ONG SRD no Facebook</a>
                        </li>
                    </ul>

                    <p class="contact-response-time">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        Respondemos em até 2 dias úteis.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
