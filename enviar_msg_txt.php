<?php
include 'db/config.php';

try {
    $sql = 'SELECT * FROM config_api';
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        foreach ($results as $result) {
            $url_api = $result['url_api'];
            $api_key = $result['api_key'];
        }
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

try {

    // URL da API para buscar as instâncias
    $parametros = "/instance/fetchInstances";
    $url = $url_api . $parametros;


    // Dados do cabeçalho (headers)
    $headers_fetch = array(
        "Content-Type: application/json",
        "apikey: " . $api_key
    );

    // Inicializa a sessão cURL
    $ch_fetch = curl_init($url);

    // Define as opções da requisição para buscar as instâncias
    curl_setopt($ch_fetch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_fetch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_fetch, CURLOPT_HTTPHEADER, $headers_fetch);

    // Executa a requisição para buscar as instâncias e obtém a resposta
    $response_fetch = curl_exec($ch_fetch);

    // Verifica por erros na requisição
    if (curl_errno($ch_fetch)) {
        $error_api = '<span class="badge rounded-pill bg-danger" style="padding:10px"><strong>API OFF-LINE</strong>: ' . curl_error($ch_fetch).'</span>';
    }else{
        $error_api = '';
    }

    // Decodifica a resposta JSON
    $instances = json_decode($response_fetch, true);

    // Fecha a sessão cURL
    curl_close($ch_fetch);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

if (isset($_POST['submit'])) {

    // Recebendo o texto da mensagem via post
    $mensagem = $_POST['mensagem'];
    $delay = $_POST['delay'];
    $instancia_select = $_POST['instancia_select'];
    // Verifica se o arquivo foi enviado
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        // Abre o arquivo em modo de leitura
        $handle = fopen($_FILES['file']['tmp_name'], "r");

        // Verifica se o arquivo foi aberto com sucesso
        if ($handle !== FALSE) {

            $parametros = "/message/sendText/";
            $url = $url_api . $parametros . $instancia_select;

            // Dados do cabeçalho (headers)
            $headers = array(
                "Content-Type: application/json",
                "apikey: " . $api_key
            );

            // Loop para ler cada linha do arquivo CSV
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                // O número de telefone está na primeira coluna do CSV
                $phoneNumber = $data[0];

                // Dados do corpo da requisição (body)
                $data = array(
                    "number" => $phoneNumber,
                    "textMessage" => array(
                        "text" => $mensagem
                    )
                );

                // Converte os dados em JSON
                $data_json = json_encode($data);

                // Inicializa a sessão cURL
                $ch = curl_init($url);

                // Define as opções da requisição
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                // Executa a requisição e obtém a resposta
                $response = curl_exec($ch);


                // Verifica por erros
                if (curl_errno($ch)) {
                    echo 'Erro na requisição cURL: ' . curl_error($ch);
                }

                // Add delay para enviar próxima mensagem
                sleep($delay);

                // Fecha a sessão cURL
                curl_close($ch);

                //var_dump($response);die;
            }
            echo "<script type='text/javascript'>alert('Mensagens enviadas com Sucesso!');";
            echo "window.location='enviar_msg_txt.php';</script>";


            // Fecha o arquivo CSV
            fclose($handle);
        } else {
            echo "Erro ao abrir o arquivo CSV.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Administração - Sistema CursoDev!</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/29.0.0/classic/ckeditor.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .containerbox {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .emoji {
            font-size: 30px;
            position: relative;
            cursor: pointer;
            margin-left: 10px;
        }

        .emoji>span {
            padding: 10px;
            border: 1px solid transparent;
            transition: 100ms linear;
        }

        .emoji span:hover {
            background-color: #fff;
            border-radius: 4px;
            border: 1px solid #e7e7e7;
            box-shadow: 0 7px 14px 0 rgb(0 0 0 / 12%);
        }

        #emoji-picker {
            padding: 6px;
            font-size: 20px;
            z-index: 1;
            position: absolute;
            display: none;
            width: 189px;
            border-radius: 4px;
            top: 53px;
            right: 0;
            background: #fff;
            border: 1px solid #e7e7e7;
            box-shadow: 0 7px 14px 0 rgb(0 0 0 / 12%);
        }

        #emoji-picker span {
            cursor: pointer;
            width: 35px;
            height: 35px;
            display: inline-block;
            text-align: center;
            padding-top: 4px;
        }

        #emoji-picker span:hover {
            background-color: #e7e7e7;
            border-radius: 4px;
        }

        .emoji-arrow {
            position: absolute;
            width: 0;
            height: 0;
            top: 0;
            right: 18px;
            box-sizing: border-box;
            border-color: transparent transparent #fff #fff;
            border-style: solid;
            border-width: 4px;
            transform-origin: 0 0 0;
            transform: rotate(135deg);
        }


        .creator {
            position: fixed;
            right: 5px;
            top: 5px;
            font-size: 13px;
            font-family: sans-serif;
            text-decoration: none;
            color: #111;
        }

        .creator:hover {
            color: deeppink;
        }

        .creator i {
            font-size: 12px;
            color: #111;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="index.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="assets/img/icon.png">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">CursoDev</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <a href="index.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="instancia.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-message-alt-add"></i>
                            <div data-i18n="Analytics">Criar Instância</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="listar_instancias.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-list-ul"></i>
                            <div data-i18n="Analytics">Listar Instâncias</div>
                        </a>
                    </li>

                    <li class="menu-item active open">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons bx bxl-whatsapp-square'></i>
                            <div data-i18n="Configurações">Disparador</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="enviar_msg_txt.php" class="menu-link">
                                    <div data-i18n="WhatsApp Api">Mensagem de Texto</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div data-i18n="WhatsApp Api">C/ Imagem (Pro)</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons bx bx-cog'></i>
                            <div data-i18n="Configurações">Configurações</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="config_api.php" class="menu-link">
                                    <div data-i18n="WhatsApp Api">Evolution Api</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div data-i18n="WhatsApp Api">Chatwoot (Pro)</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div data-i18n="WhatsApp Api">Typebot (Pro)</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div data-i18n="WhatsApp Api">Woocommerce (Pro)</div>
                                </a>
                            </li>
                        </ul>                   
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="container">
                            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Importar /</span> Enviar em massa</h4>
                            <form class="form-horizontal" action="" method="post" id="formCadastro" enctype="multipart/form-data" name="cadastrar">
                                <div class="card">
                                    <div class="card-body">
                                        <?php echo $error_api?>
                                        <div class="center">
                                            <div class="mb-3">
                                                <label for="defaultSelect" class="form-label">Selecione a Instância</label>
                                                <select id="defaultSelect" class="form-select" name="instancia_select">
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($instances as $instance): ?>
                                                        <option value="<?php echo $instance['instance']['instanceName']; ?>"><?php echo $instance['instance']['instanceName']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="containerbox">
                                                <div class="input-group" style="padding-bottom: 10px;">
                                                    <span class="input-group-text">Mensagem</span>
                                                    <textarea id="text-area" class="form-control" name="mensagem" aria-label="With textarea" placeholder="Digite a mensagem...."></textarea>
                                                </div>
                                                <div class="emoji">
                                                    <span>🙂</span>
                                                    <div id="emoji-picker">
                                                        <div class="emoji-arrow"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group" style="padding-bottom: 10px;">
                                            <span class="input-group-text">Tempo de Delay entre mensagens</span>
                                            <input type="number" class="form-control" name="delay" id="basic-url3" aria-describedby="basic-addon34">
                                        </div>
                                        <div class="input-group" style="padding-bottom: 10px;">
                                            <input type="file" name="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                                        </div>
                                        <button class="btn btn-outline-primary" type="submit" id="inputGroupFileAddon04" name="submit">Carregar & Enviar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- / Content -->
                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <script>
        var emojiPicker = function() {
            var i = null;
            var index = null;
            var emojiCode = [
                128526, 128565, 129299, 129322, 128516, 128517, 128518, 128519, 128520,
                128521, 128522, 128523, 128524, 128525, 128526, 128527, 128528, 128529,
                128530, 128531, 128532, 128533, 128534, 128535, 128536, 128537, 128538,
                128539, 128540, 128741, 128542, 128543, 128544, 128545, 128546, 128547,
                128548, 128549, 129297, 129395, 128070, 128073, 128226, 128227
            ];

            for (index = 0; index <= emojiCode.length - 1; index++) {
                document.querySelector("#emoji-picker").innerHTML += "<span class='my-emoji'>" + "&#" + emojiCode[index] + "</span>";
            }

            $(document).on("click", ".my-emoji", function() {
                var textArea = $('#text-area');
                textArea.val(textArea.val() + $(this).text());
                $("#emoji-picker").hide();
                textArea.focus();
            });
        }

        emojiPicker();

        $(".emoji").click(function(e) {
            e.preventDefault();
            $("#emoji-picker").toggle();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Mostra o modal ao clicar no botão de enviar
            $("#myForm").on("submit", function() {
                $("#myModal").show();
            });
        });
    </script>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>