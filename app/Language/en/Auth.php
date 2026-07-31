<?php

declare(strict_types=1);

return [
    // Exceptions
    'unknownAuthenticator'  => '{0} nao e um autenticador valido.',
    'unknownUserProvider'   => 'Nao foi possivel determinar o provedor de usuario a ser usado.',
    'invalidUser'           => 'Nao foi possivel localizar o usuario especificado.',
    'bannedUser'            => 'Nao e possivel fazer login porque sua conta esta banida.',
    'logOutBannedUser'      => 'Voce foi desconectado porque sua conta foi banida.',
    'badAttempt'            => 'Nao foi possivel fazer login. Verifique suas credenciais.',
    'noPassword'            => 'Nao e possivel validar um usuario sem senha.',
    'invalidPassword'       => 'Nao foi possivel fazer login. Verifique sua senha.',
    'noToken'               => 'Toda requisicao deve ter um bearer token no cabecalho {0}.',
    'badToken'              => 'O token de acesso e invalido.',
    'oldToken'              => 'O token de acesso expirou.',
    'noUserEntity'          => 'A entidade de usuario deve ser fornecida para validar senha.',
    'invalidEmail'          => 'Nao foi possivel verificar se o e-mail "{0}" corresponde ao registro.',
    'unableSendEmailToUser' => 'Desculpe, houve um problema ao enviar o e-mail. Nao foi possivel enviar para "{0}".',
    'throttled'             => 'Muitas requisicoes deste endereco IP. Tente novamente em {0} segundos.',
    'notEnoughPrivilege'    => 'Voce nao tem permissao necessaria para executar esta operacao.',

    // JWT Exceptions
    'invalidJWT'     => 'O token e invalido.',
    'expiredJWT'     => 'O token expirou.',
    'beforeValidJWT' => 'O token ainda nao esta disponivel.',

    'email'           => 'Endereco de e-mail',
    'username'        => 'Nome de usuario',
    'password'        => 'Senha',
    'passwordConfirm' => 'Senha (novamente)',
    'haveAccount'     => 'Ja tem uma conta?',
    'token'           => 'Token',

    // Buttons
    'confirm' => 'Confirmar',
    'send'    => 'Enviar',

    // Registration
    'register'         => 'Cadastrar',
    'registerDisabled' => 'No momento, o cadastro nao esta permitido.',
    'registerSuccess'  => 'Bem-vindo(a)!',

    // Login
    'login'              => 'Entrar',
    'needAccount'        => 'Precisa de uma conta?',
    'rememberMe'         => 'Lembrar de mim?',
    'forgotPassword'     => 'Esqueceu sua senha?',
    'useMagicLink'       => 'Usar link de login',
    'magicLinkSubject'   => 'Seu link de login',
    'magicTokenNotFound' => 'Nao foi possivel verificar o link.',
    'magicLinkExpired'   => 'Desculpe, o link expirou.',
    'checkYourEmail'     => 'Verifique seu e-mail!',
    'magicLinkDetails'   => 'Acabamos de enviar um e-mail com um link de login. Ele e valido por {0} minutos.',
    'magicLinkDisabled'  => 'O uso de MagicLink nao esta permitido no momento.',
    'successLogout'      => 'Voce saiu com sucesso.',
    'backToLogin'        => 'Voltar para o login',

    // Passwords
    'errorPasswordLength'       => 'As senhas devem ter pelo menos {0, number} caracteres.',
    'suggestPasswordLength'     => 'Frases-senha com ate 255 caracteres geram senhas mais seguras e faceis de lembrar.',
    'errorPasswordCommon'       => 'A senha nao pode ser uma senha comum.',
    'suggestPasswordCommon'     => 'A senha foi comparada com mais de 65 mil senhas comuns ou vazadas.',
    'errorPasswordPersonal'     => 'A senha nao pode conter informacoes pessoais re-hashadas.',
    'suggestPasswordPersonal'   => 'Nao use variacoes do seu e-mail ou nome de usuario na senha.',
    'errorPasswordTooSimilar'   => 'A senha e muito parecida com o nome de usuario.',
    'suggestPasswordTooSimilar' => 'Nao use partes do seu nome de usuario na senha.',
    'errorPasswordPwned'        => 'A senha {0} foi exposta em violacao de dados e apareceu {1, number} vezes em {2} senhas comprometidas.',
    'suggestPasswordPwned'      => '{0} nunca deve ser usada como senha. Se voce usa essa senha em algum lugar, altere imediatamente.',
    'errorPasswordEmpty'        => 'A senha e obrigatoria.',
    'errorPasswordTooLongBytes' => 'A senha nao pode exceder {param} bytes.',
    'passwordChangeSuccess'     => 'Senha alterada com sucesso',
    'userDoesNotExist'          => 'A senha nao foi alterada. O usuario nao existe',
    'resetTokenExpired'         => 'Desculpe. Seu token de redefinicao expirou.',

    // Email Globals
    'emailInfo'      => 'Algumas informacoes sobre a pessoa:',
    'emailIpAddress' => 'Endereco IP:',
    'emailDevice'    => 'Dispositivo:',
    'emailDate'      => 'Data:',

    // 2FA
    'email2FATitle'       => 'Autenticacao de dois fatores',
    'confirmEmailAddress' => 'Confirme seu endereco de e-mail.',
    'emailEnterCode'      => 'Confirme seu e-mail',
    'emailConfirmCode'    => 'Digite o codigo de 6 digitos que enviamos para seu e-mail.',
    'email2FASubject'     => 'Seu codigo de autenticacao',
    'email2FAMailBody'    => 'Seu codigo de autenticacao e:',
    'invalid2FAToken'     => 'O codigo esta incorreto.',
    'need2FA'             => 'Voce precisa concluir a verificacao em dois fatores.',
    'needVerification'    => 'Verifique seu e-mail para concluir a ativacao da conta.',

    // Activate
    'emailActivateTitle'    => 'Ativacao por e-mail',
    'emailActivateBody'     => 'Acabamos de enviar um e-mail com um codigo para confirmar seu endereco de e-mail. Copie o codigo e cole abaixo.',
    'emailActivateSubject'  => 'Seu codigo de ativacao',
    'emailActivateMailBody' => 'Use o codigo abaixo para ativar sua conta e comecar a usar o site.',
    'invalidActivateToken'  => 'O codigo esta incorreto.',
    'needActivate'          => 'Voce precisa concluir seu cadastro confirmando o codigo enviado ao seu e-mail.',
    'activationBlocked'     => 'Voce precisa ativar sua conta antes de entrar.',

    // Groups
    'unknownGroup' => '{0} nao e um grupo valido.',
    'missingTitle' => 'Grupos precisam ter um titulo.',

    // Permissions
    'unknownPermission' => '{0} nao e uma permissao valida.',
];
