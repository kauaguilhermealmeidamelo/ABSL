{{--
    Layout institucional de e-mails — ABSL / Grêmio Athos Bulcão

    Compatibilidade:
    - Layout baseado em tabelas;
    - CSS essencial inline;
    - Responsividade por media query;
    - Compatível com clientes de e-mail antigos;
    - Não depende do CSS do frontend/Vue.

    Variáveis esperadas pelo Mailable:

    $assunto
    $preheader
    $saudacao
    $nome
    $mensagem
    $titulo
    $conteudo
    $ctaTexto
    $ctaUrl
    $logoPath
    $temLogo
    $rodape
    $informacoes

    Logo:
    backend/public/images/logo.png

    O Mailable deve fornecer:

    $logoPath = public_path('images/logo.png');
    $temLogo = file_exists($logoPath);
--}}

<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >

    <meta
        name="x-apple-disable-message-reformatting"
    >

    <meta
        name="color-scheme"
        content="light only"
    >

    <title>
        {{ $assunto ?? 'ABSL — Grêmio Athos Bulcão' }}
    </title>

    <style>
        @media only screen and (max-width: 620px) {

            .absl-card {
                width: 100% !important;
                max-width: 100% !important;
            }

            .absl-pad {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .absl-header-pad {
                padding: 22px 20px !important;
            }

            .absl-brand-name {
                font-size: 22px !important;
            }

            .absl-brand-description {
                font-size: 11px !important;
            }

            .absl-title {
                font-size: 28px !important;
                line-height: 34px !important;
            }

            .absl-content {
                font-size: 15px !important;
                line-height: 23px !important;
            }

            .absl-cta {
                display: block !important;
                width: auto !important;
                text-align: center !important;
            }

            .absl-info-label,
            .absl-info-value {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .absl-info-label {
                padding-top: 12px !important;
                padding-bottom: 3px !important;
            }

            .absl-info-value {
                padding-top: 0 !important;
                padding-bottom: 12px !important;
            }

            .absl-header-identifier {
                display: none !important;
            }
        }
    </style>

</head>

<body
    style="
        margin:0;
        padding:0;
        width:100%;
        background-color:#EEF1F6;
        -webkit-text-size-adjust:100%;
        -ms-text-size-adjust:100%;
    "
>

    {{-- ============================================================
         PREHEADER
    ============================================================= --}}

    <div
        style="
            display:none;
            max-height:0;
            overflow:hidden;
            font-size:1px;
            line-height:1px;
            color:#EEF1F6;
            opacity:0;
        "
    >
        {{ $preheader ?? ($assunto ?? 'ABSL — Grêmio Athos Bulcão') }}
    </div>


    {{-- ============================================================
         CONTAINER EXTERNO
    ============================================================= --}}

    <table
        role="presentation"
        cellpadding="0"
        cellspacing="0"
        border="0"
        width="100%"
        style="
            width:100%;
            background-color:#EEF1F6;
        "
    >

        <tr>

            <td
                align="center"
                style="
                    padding:28px 12px;
                "
            >

                {{-- ====================================================
                     CARD PRINCIPAL
                ===================================================== --}}

                <table
                    role="presentation"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    width="600"
                    class="absl-card"
                    style="
                        width:600px;
                        max-width:600px;
                        background-color:#FFFFFF;
                        border:1px solid #ECECF3;
                        border-radius:16px;
                        overflow:hidden;
                    "
                >

                    {{-- =================================================
                         CABEÇALHO
                    ================================================== --}}

                    <tr>

                        <td
                            class="absl-header-pad"
                            style="
                                padding:24px 32px;
                                background-color:#0F2038;
                            "
                        >

                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                width="100%"
                                style="width:100%;"
                            >

                                <tr>

                                    {{-- LOGO --}}

                                    <td
                                        width="54"
                                        valign="middle"
                                        style="
                                            width:54px;
                                            padding-right:14px;
                                        "
                                    >

                                        @if (($temLogo ?? false) && !empty($logoPath))

                                            <img
                                                src="{{ $message->embed($logoPath) }}"
                                                width="48"
                                                height="48"
                                                alt="ABSL — Grêmio Athos Bulcão"
                                                style="
                                                    display:block;
                                                    width:48px;
                                                    height:48px;
                                                    border:0;
                                                    border-radius:10px;
                                                    outline:none;
                                                    text-decoration:none;
                                                "
                                            >

                                        @else

                                            {{-- Fallback caso a logo não exista --}}

                                            <table
                                                role="presentation"
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="48"
                                                height="48"
                                                style="
                                                    width:48px;
                                                    height:48px;
                                                    background-color:#16509B;
                                                    border-radius:10px;
                                                "
                                            >

                                                <tr>

                                                    <td
                                                        align="center"
                                                        valign="middle"
                                                        style="
                                                            font-family:Arial,Helvetica,sans-serif;
                                                            font-size:17px;
                                                            font-weight:bold;
                                                            color:#FFFFFF;
                                                        "
                                                    >
                                                        ABSL
                                                    </td>

                                                </tr>

                                            </table>

                                        @endif

                                    </td>


                                    {{-- MARCA --}}

                                    <td valign="middle">

                                        <div
                                            class="absl-brand-name"
                                            style="
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:24px;
                                                line-height:28px;
                                                font-weight:bold;
                                                color:#FFFFFF;
                                                letter-spacing:1px;
                                            "
                                        >
                                            ABSL
                                        </div>

                                        <div
                                            class="absl-brand-description"
                                            style="
                                                margin-top:2px;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:12px;
                                                line-height:17px;
                                                color:#BFD0E5;
                                            "
                                        >
                                            Grêmio Athos Bulcão
                                        </div>

                                    </td>


                                    {{-- IDENTIFICADOR --}}

                                    <td
                                        align="right"
                                        valign="middle"
                                        class="absl-header-identifier"
                                        style="
                                            font-family:Arial,Helvetica,sans-serif;
                                            font-size:10px;
                                            line-height:15px;
                                            font-weight:bold;
                                            letter-spacing:1px;
                                            color:#F5A623;
                                            text-transform:uppercase;
                                        "
                                    >
                                        COMUNICAÇÃO<br>
                                        ABSL
                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>


                    {{-- =================================================
                         FRISO DOURADO
                    ================================================== --}}

                    <tr>

                        <td
                            bgcolor="#F5A623"
                            height="5"
                            style="
                                height:5px;
                                line-height:5px;
                                font-size:0;
                                background-color:#F5A623;
                            "
                        >
                            &nbsp;
                        </td>

                    </tr>


                    {{-- =================================================
                         PADRÃO / IDENTIDADE
                    ================================================== --}}

                    <tr>

                        <td
                            style="
                                padding:0;
                                background-color:#16509B;
                            "
                        >

                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                width="100%"
                                style="width:100%;"
                            >

                                <tr>

                                    <td
                                        width="22%"
                                        height="8"
                                        bgcolor="#1B63BD"
                                        style="
                                            width:22%;
                                            height:8px;
                                            background-color:#1B63BD;
                                        "
                                    >
                                    </td>

                                    <td
                                        width="28%"
                                        height="8"
                                        bgcolor="#F5A623"
                                        style="
                                            width:28%;
                                            height:8px;
                                            background-color:#F5A623;
                                        "
                                    >
                                    </td>

                                    <td
                                        width="20%"
                                        height="8"
                                        bgcolor="#0F2038"
                                        style="
                                            width:20%;
                                            height:8px;
                                            background-color:#0F2038;
                                        "
                                    >
                                    </td>

                                    <td
                                        width="30%"
                                        height="8"
                                        bgcolor="#1B63BD"
                                        style="
                                            width:30%;
                                            height:8px;
                                            background-color:#1B63BD;
                                        "
                                    >
                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>


                    {{-- =================================================
                         SAUDAÇÃO
                    ================================================== --}}

                    @if (!empty($saudacao) || !empty($nome))

                        <tr>

                            <td
                                class="absl-pad"
                                style="
                                    padding:30px 36px 8px;
                                    font-family:Arial,Helvetica,sans-serif;
                                "
                            >

                                @if (!empty($saudacao))

                                    <p
                                        style="
                                            margin:0;
                                            font-family:Arial,Helvetica,sans-serif;
                                            font-size:15px;
                                            line-height:22px;
                                            color:#6B7C93;
                                        "
                                    >
                                        {{ $saudacao }}
                                    </p>

                                @endif


                                @if (!empty($nome))

                                    <p
                                        style="
                                            margin:5px 0 0;
                                            font-family:Georgia,'Times New Roman',serif;
                                            font-size:26px;
                                            line-height:32px;
                                            font-weight:bold;
                                            color:#0F2038;
                                        "
                                    >
                                        {{ $nome }}
                                    </p>

                                @endif

                            </td>

                        </tr>

                    @endif


                    {{-- =================================================
                         TÍTULO PRINCIPAL
                    ================================================== --}}

                    @if (!empty($titulo))

                        <tr>

                            <td
                                class="absl-pad"
                                style="
                                    padding:18px 36px 8px;
                                    font-family:Georgia,'Times New Roman',serif;
                                "
                            >

                                <h1
                                    class="absl-title"
                                    style="
                                        margin:0;
                                        font-family:Georgia,'Times New Roman',serif;
                                        font-size:34px;
                                        line-height:40px;
                                        font-weight:bold;
                                        color:#0F2038;
                                    "
                                >
                                    {{ $titulo }}
                                </h1>


                                {{-- Detalhe visual --}}

                                <table
                                    role="presentation"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        margin-top:12px;
                                    "
                                >

                                    <tr>

                                        <td
                                            width="42"
                                            height="4"
                                            bgcolor="#F5A623"
                                            style="
                                                width:42px;
                                                height:4px;
                                                background-color:#F5A623;
                                                border-radius:999px;
                                                font-size:0;
                                                line-height:4px;
                                            "
                                        >
                                        </td>

                                        <td
                                            width="8"
                                            style="
                                                width:8px;
                                                font-size:0;
                                                line-height:0;
                                            "
                                        >
                                            &nbsp;
                                        </td>

                                        <td
                                            width="14"
                                            height="4"
                                            bgcolor="#16509B"
                                            style="
                                                width:14px;
                                                height:4px;
                                                background-color:#16509B;
                                                border-radius:999px;
                                                font-size:0;
                                                line-height:4px;
                                            "
                                        >
                                        </td>

                                    </tr>

                                </table>

                            </td>

                        </tr>

                    @endif


                    {{-- =================================================
                         MENSAGEM
                    ================================================== --}}

                    @if (!empty($mensagem))

                        <tr>

                            <td
                                class="absl-pad absl-content"
                                style="
                                    padding:18px 36px 10px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:15px;
                                    line-height:24px;
                                    color:#1C2333;
                                "
                            >
                                {!! nl2br(e($mensagem)) !!}
                            </td>

                        </tr>

                    @endif


                    {{-- =================================================
                         CONTEÚDO PRINCIPAL
                    ================================================== --}}

                    @if (!empty($conteudo))

                        <tr>

                            <td
                                class="absl-pad absl-content"
                                style="
                                    padding:12px 36px 18px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:15px;
                                    line-height:24px;
                                    color:#1C2333;
                                "
                            >
                                {!! $conteudo !!}
                            </td>

                        </tr>

                    @endif


                    {{-- =================================================
                         CTA
                    ================================================== --}}

                    @if (!empty($ctaUrl))

                        <tr>

                            <td
                                class="absl-pad"
                                style="
                                    padding:12px 36px 30px;
                                "
                            >

                                <table
                                    role="presentation"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                >

                                    <tr>

                                        <td
                                            align="center"
                                            bgcolor="#16509B"
                                            style="
                                                background-color:#16509B;
                                                border-radius:999px;
                                            "
                                        >

                                            <a
                                                href="{{ $ctaUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="absl-cta"
                                                style="
                                                    display:inline-block;
                                                    padding:13px 24px;
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:14px;
                                                    line-height:18px;
                                                    font-weight:bold;
                                                    color:#FFFFFF;
                                                    text-decoration:none;
                                                    border-radius:999px;
                                                "
                                            >
                                                {{ $ctaTexto ?? 'ACESSAR O ABSL' }}
                                                &nbsp; →
                                            </a>

                                        </td>

                                    </tr>

                                </table>

                            </td>

                        </tr>

                    @endif


                    {{-- =================================================
                         BLOCO INFORMATIVO
                    ================================================== --}}

                    @if (!empty($informacoes) && is_iterable($informacoes))

                        <tr>

                            <td
                                class="absl-pad"
                                style="
                                    padding:0 36px 28px;
                                "
                            >

                                <table
                                    role="presentation"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    width="100%"
                                    style="
                                        width:100%;
                                        border:1px solid #ECECF3;
                                        border-radius:12px;
                                        background-color:#F7F8FC;
                                    "
                                >

                                    @foreach ($informacoes as $informacao)

                                        @php
                                            $label = is_array($informacao)
                                                ? ($informacao['label'] ?? '')
                                                : '';

                                            $valor = is_array($informacao)
                                                ? ($informacao['valor'] ?? '')
                                                : '';
                                        @endphp

                                        <tr>

                                            {{-- LABEL --}}

                                            <td
                                                class="absl-info-label"
                                                width="38%"
                                                valign="top"
                                                style="
                                                    width:38%;
                                                    padding:13px 16px;
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:12px;
                                                    line-height:18px;
                                                    font-weight:bold;
                                                    color:#6B7C93;
                                                    border-bottom:1px solid #ECECF3;
                                                "
                                            >
                                                {{ $label }}
                                            </td>


                                            {{-- VALOR --}}

                                            <td
                                                class="absl-info-value"
                                                width="62%"
                                                valign="top"
                                                style="
                                                    width:62%;
                                                    padding:13px 16px;
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:14px;
                                                    line-height:20px;
                                                    font-weight:600;
                                                    color:#1C2333;
                                                    border-bottom:1px solid #ECECF3;
                                                "
                                            >
                                                {{ $valor }}
                                            </td>

                                        </tr>

                                    @endforeach

                                </table>

                            </td>

                        </tr>

                    @endif


                    {{-- =================================================
                         ASSINATURA
                    ================================================== --}}

                    <tr>

                        <td
                            class="absl-pad"
                            style="
                                padding:6px 36px 28px;
                                font-family:Arial,Helvetica,sans-serif;
                            "
                        >

                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                            >

                                <tr>

                                    <td
                                        width="4"
                                        bgcolor="#F5A623"
                                        style="
                                            width:4px;
                                            background-color:#F5A623;
                                            border-radius:999px;
                                            font-size:0;
                                            line-height:0;
                                        "
                                    >
                                        &nbsp;
                                    </td>

                                    <td
                                        style="
                                            padding-left:12px;
                                        "
                                    >

                                        <p
                                            style="
                                                margin:0;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:13px;
                                                line-height:19px;
                                                color:#6B7C93;
                                            "
                                        >
                                            Atenciosamente,
                                        </p>

                                        <p
                                            style="
                                                margin:2px 0 0;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:14px;
                                                line-height:20px;
                                                font-weight:bold;
                                                color:#0F2038;
                                            "
                                        >
                                            Grêmio Athos Bulcão — ABSL
                                        </p>

                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>


                    {{-- =================================================
                         RODAPÉ
                    ================================================== --}}

                    <tr>

                        <td
                            class="absl-pad"
                            bgcolor="#0F2038"
                            style="
                                padding:22px 36px;
                                background-color:#0F2038;
                                font-family:Arial,Helvetica,sans-serif;
                            "
                        >

                            <p
                                style="
                                    margin:0 0 8px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:11px;
                                    line-height:17px;
                                    color:#AFC0D5;
                                "
                            >
                                {{ $rodape ?? 'Esta é uma mensagem enviada pelo Grêmio Athos Bulcão (ABSL).' }}
                            </p>

                            <p
                                style="
                                    margin:0;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:11px;
                                    line-height:17px;
                                    color:#FFFFFF;
                                    font-weight:bold;
                                "
                            >
                                ABSL — Grêmio Athos Bulcão
                            </p>

                            <p
                                style="
                                    margin:4px 0 0;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:10px;
                                    line-height:15px;
                                    color:#7F95B0;
                                "
                            >
                                Representação estudantil · Informação · Participação
                            </p>

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

</body>

</html>
