<?php
include 'db/config.php';

try {
    $sql = 'SELECT * FROM config_api';
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        foreach ($results as $result) {
            $instancia_ID = $result['id'];
            $url_api = $result['url_api'];
            $api_key = $result['api_key'];
        }
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

if (isset($_POST['cadastrar_config'])) {

    $url_api = $_POST['url_api'];
    $api_key = $_POST['api_key'];

    try {
        // Verifica se existem registros no banco de dados
        $sql_check = 'SELECT COUNT(*) FROM config_api';
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute();
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            echo "<script>alert('Já existe dados cadastrados. Use o botão Limpar.');</script>";
        } else {
            // Insere os dados no banco de dados
            $sql = 'INSERT INTO config_api (url_api, api_key) 
                            VALUES (:url_api, :api_key)';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':url_api', $url_api);
            $stmt->bindParam(':api_key', $api_key);
            $stmt->execute();

            // Verifica se a inserção foi bem-sucedida
            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Dados cadastrados com sucesso!'); window.location.href = 'config_api.php';</script>";
            } else {
                echo "<script>alert('Ocorreu um erro ao cadastrar os dados.'); window.location.href = 'config_api.php';</script>";
            }
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

if (isset($_POST['limpar_config'])) {

    try {

        // Limpa a tabela config_api
        $sql = 'DELETE FROM config_api';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Verifica se a operação foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Dados limpos com sucesso!'); window.location.href = 'config_api.php';</script>";
        } else {
            echo "<script>alert('Ocorreu um erro ao limpar os dados.'); window.location.href = 'config_api.php';</script>";
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Configurações API - CursoDev</title>
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

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons bx bxl-whatsapp-square'></i>
                            <div data-i18n="Configurações">Disparador</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
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

                    <li class="menu-item  active open">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons bx bx-cog'></i>
                            <div data-i18n="Configurações">Configurações</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
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
                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Configurações /</span> Evolution API</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <!-- Account -->
                                    <form id="formAccountSettings" method="POST" enctype="multipart/form-data" name="cadastrar_config">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="titulo" class="form-label">URL API</label>
                                                    <input class="form-control" type="text" id="url_api" name="url_api" value="<?php if (!empty($url_api)) {
                                                                                                                                    echo $url_api;
                                                                                                                                } ?>" autofocus />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="url" class="form-label">API KEY</label>
                                                    <input class="form-control" type="text" name="api_key" id="api_key" value="<?php if (!empty($api_key)) {
                                                                                                                                    echo $api_key;
                                                                                                                                } ?>" />
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button type="submit" class="btn btn-primary me-2" name="cadastrar_config">Cadastrar Dados</button>
                                                <button type="submit" class="btn btn-outline-secondary" name="limpar_config">Limpar Dados</button>
                                            </div>
                                    </form>
                                </div>
                                <!-- /Account -->
                            </div>
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