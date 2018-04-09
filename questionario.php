<?php
$conf = include("conf.php");

define("TIPO_EMOCIONAL", 1);
define("TIPO_RECORRENCIA", 2);

$db = new mysqli($conf["servername"], $conf["username"], $conf["password"], $conf["dbname"]);

if ($db->connect_error) {
    die("Erro ao se conectar no banco de dados: " . $db->connect_error);
}

$db->set_charset("utf8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <link rel="tecmides-icon" sizes="76x76" href="assets/img/logo.png">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Questionário - tecmides</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link href="https://fonts.googleapis.com/css?family=Dosis:400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/material-kit.css" rel="stylesheet"/>
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/aos.css" />
    <link rel='stylesheet' type='text/css' href='assets/css/gew.css'/>
    <link rel='stylesheet' type='text/css' href='assets/css/questionario.css'/>
    <link rel='stylesheet' type='text/css' href='assets/css/tecmides.css'/>

    <!-- Scripts  -->
    <!--<script src="assets/js/aos.js"></script> -->
    <script src='assets/js/gew.js'></script>
    <!--<script src='assets/js/questionario.js'></script>-->

</head>
<body class="components-page">
    <!-- Navbar -->
    <nav class="navbar navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-index">
                    <span class="sr-only">Alterar navegação</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="index.html">
                    <div class="logo-container">
                        <img src="assets/img/logo.svg" alt="tecmides-logo" style="padding-left: 10px;">
                    </div>
                </a>
            </div>

            <div class="collapse navbar-collapse" id="navigation-index">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="index.html">
                            TECMIDES
                        </a>
                    </li>
                </ul>
                <!-- Links da barra de navegação -->
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="questionario.php">
                            QUESTIONÁRIO
                        </a>
                    </li>
                    <li>
                        <a href="sobre.html">
                            SOBRE
                        </a>
                    </li>

                </ul>
                <!-- -->
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="wrapper">
        <div class="header header-filter" style="height:220px; padding-top:52px; background-image: url('assets/img/header.svg');">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="title text-center dosis-bold">Questionário</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="main main-raised" id="questoes">
            <div class="section section-basic">
                <div class="container" style="padding-left: 80px; padding-right: 80px;">
                    <form action="questionario.php" method="POST">
                        <?php
                        try
                        {
                            session_start();

                            if ( isset($_POST["validacao"]) ) {
                                $result = $db->query("SELECT * FROM aluno WHERE matricula='{$_POST["matricula"]}' AND id_turma='{$_SESSION["turma"]}'");
                                $ehAluno = $result->num_rows > 0;

                                if($ehAluno) {
                                    $_SESSION["ehAluno"] = true;
                                    $_SESSION["nome"] = $_POST["nome"];
                                    $_SESSION["matricula"] = $_POST["matricula"];
                                    $_SESSION["passo"] = 1;
                                }
                                else {
                                    throw new Exception("Você não é um aluno cadastrado para responder esse questionário!");
                                }

                                $result->close();
                            }

                            if( !isset($_SESSION["ehAluno"]) && !isset($_POST["turmaSelecionada"]) ) {
                                $result = $db->query("SELECT * FROM turma");
                                echo "<center>";
                                echo "<h2 class='dosis-bold' style='text-align: center;'>Seleciona e sua turma</h2><br>";
                                echo "<select name='turma'>";
                                while($turma = $result->fetch_assoc())
                                {
                                    echo "
                                    <option value='{$turma['id']}'>{$turma['periodo_letivo']} - {$turma['disciplina']} - TURMA {$turma['turma']}</option>
                                    ";
                                }
                                echo "</select>
                                <div align='center' style='padding: 50px;'>
                                <button class='btn btn-success btn-lg' type='submit' name='turmaSelecionada'>CONTINUAR</button>
                                </div>";
                                echo "</center>";

                            } else if( isset($_POST["turmaSelecionada"]) ) {
                                $id = $_POST['turma'];
                                $_SESSION["turma"] = $id;
                                $termo = $db->query("SELECT termo FROM turma WHERE id=$id");
                                $textoTermo = $termo->fetch_assoc()["termo"];
                                echo "
                                <h2 class='dosis-bold' style='text-align: center;'>TERMO DE COMPROMISSO</h2><br>
                                <div class='body-paragraph'>
                                <p>Prezado(a) participante:</p>
                                <p>"; print_r($textoTermo);
                                echo "</p><br>

                                <div class='form-inline'>
                                <div class='form-group'>
                                Eu, &nbsp
                                <input type='text' name='nome' value='' placeholder='' class='form-control' maxlength='60' style='width: 300px;' required/>
                                </div>
                                <div class='form-group'>
                                &nbsp, com número do cartão UFRGS  &nbsp
                                <input type='number' name='matricula' value='' placeholder='' class='form-control' maxlength='8' style='width: 100px;' required/>
                                &nbsp , concordo em participar da pesquisa.
                                </div>
                                </div>
                                </div>
                                </p><br><br>
                                <div align='center' style='padding: 10px;'>
                                <button class='btn btn-success btn-lg' type='submit' name='validacao'>CONTINUAR</button>
                                </div>
                                ";
                            }
                            else if( isset($_SESSION["ehAluno"]) ) {
                                if( isset($_POST["q{$_SESSION["passo"]}"]) ) {
                                    $_SESSION["questoes"]["{$_SESSION["passo"]}"] = $_POST["q{$_SESSION["passo"]}"];

                                    $_SESSION["passo"]++;
                                }

                                $queryQuestoes = $db->query("SELECT id_questao, enunciado, id_tipo_questao FROM questao WHERE id_questao='{$_SESSION["passo"]}'");

                                if($queryQuestoes->num_rows > 0) {
                                    $questao = $queryQuestoes->fetch_assoc();

                                    echo "
                                    <div style='text-align: center'>
                                    <h2 class='dosis-bold'>Questão {$questao["id_questao"]}</h2>
                                    <p class='body-paragraph-center'>{$questao["enunciado"]}</p>
                                    </div>
                                    ";

                                    if($questao["id_tipo_questao"] == TIPO_EMOCIONAL) {
                                        echo "
                                        <div id='gew-q{$questao["id_questao"]}' class='gew' data-input='q{$questao["id_questao"]}' data-required></div>
                                        ";
                                    }
                                    else {
                                        echo "
                                        <div style='width: 150px; margin: 0 auto;'>
                                        <div class='radio'>
                                        <label class='roboto-400'>
                                        <input type='radio' name='q{$questao["id_questao"]}' value='Nunca' required>
                                        &nbsp Nunca
                                        </label>
                                        </div>
                                        <div class='radio'>
                                        <label>
                                        <input type='radio' name='q{$questao["id_questao"]}' value='Raramente'>
                                        &nbsp Raramente
                                        </label>
                                        </div>
                                        <div class='radio'>
                                        <label>
                                        <input type='radio' name='q{$questao["id_questao"]}' value='Algumas vezes'>
                                        &nbsp Algumas vezes
                                        </label>
                                        </div>
                                        <div class='radio'>
                                        <label>
                                        <input type='radio' name='q{$questao["id_questao"]}' value='Quase sempre'>
                                        &nbsp Quase sempre
                                        </label>
                                        </div>
                                        <div class='radio'>
                                        <label>
                                        <input type='radio' name='q{$questao["id_questao"]}' value='Sempre'>
                                        &nbsp Sempre
                                        </label>
                                        </div>
                                        </div>
                                        ";
                                    }

                                    echo "
                                    <button class='btn btn-success' type='submit' name='continuar'>CONTINUAR</button>
                                    ";
                                }
                                else {
                                    foreach($_SESSION["questoes"] as $id_questao => $resposta ) {
                                        $respostaAtual = $db->query("SELECT * FROM resposta WHERE id_questao='{$id_questao}' AND id_aluno='{$_SESSION["matricula"]}'");

                                        if( $respostaAtual->num_rows > 0 )
                                        {
                                            $updateResposta = $db->prepare("UPDATE resposta SET resposta=? WHERE id_questao=? AND id_aluno=?");
                                            $updateResposta->bind_param("sii", $resposta, $id_questao, $_SESSION["matricula"]);
                                            if( !$updateResposta->execute() ) {
                                                $updateResposta->close();
                                                throw new Exception($updateResposta->error);
                                            }

                                            $updateResposta->close();

                                        }
                                        else
                                        {
                                            $insertResposta = $db->prepare("INSERT INTO resposta (resposta, id_questao, id_aluno) VALUES (?,?,?)");
                                            $insertResposta->bind_param("sii", $resposta, $id_questao, $_SESSION["matricula"]);

                                            if( !$insertResposta->execute() ) {
                                                $insertResposta->close();
                                                throw new Exception($insertResposta->error);
                                            }

                                            $insertResposta->close();
                                        }

                                        $respostaAtual->close();
                                    }

                                    session_destroy();

                                    echo "
                                    <div align='center'>
                                    <h2 class='roboto-400'>Obrigado!</h2>
                                    <p class='roboto-400'>Suas respostas foram salvas!</p><br><br>
                                    <a href='./' class='btn btn-warning'>VOLTAR</a>
                                    </div>

                                    ";
                                }

                                $queryQuestoes->close();
                            }
                        }
                        catch(Exception $e) {
                            session_destroy();

                            echo "
                            <div style='text-align: center;'>
                            <h3 class='dosis-bold'>ERRO</h3>
                            <h4 class='' style='padding-bottom: 50px;'>{$e->getMessage()}</h4>
                            <button type='submit' class='btn btn-info'>VOLTAR</button>
                            </div>
                            ";
                        }

                        $db->close();
                        ?>
                    </form>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <nav class="pull-left">
                    <ul>
                        <li>
                            <a href="index.html">
                                PÁGINA INICIAL
                            </a>
                        </li>
                        <li>
                            <a href="questionario.php">
                                QUESTIONARIO
                            </a>
                        </li>
                    </ul>
                </nav>
                <nav class="pull-right">
                    <ul>
                        <li><a href="sobre.html">SOBRE</a>
                        </ul>
                    </nav>
                </div>
            </footer>

        </div>


    </body>

    <!--   Core JS Files   -->
    <script src="assets/js/jquery.min.js" type="text/javascript"></script>
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/js/material.min.js"></script>

    <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
    <script src="assets/js/nouislider.min.js" type="text/javascript"></script>

    <!--  Plugin for the Datepicker, full documentation here: http://www.eyecon.ro/bootstrap-datepicker/ -->
    <script src="assets/js/bootstrap-datepicker.js" type="text/javascript"></script>

    <!-- Control Center for Material Kit: activating the ripples, parallax effects, scripts from the example pages etc -->
    <script src="assets/js/material-kit.js" type="text/javascript"></script>

    <!--
    <!-- Animate On Screen
    <script>
    AOS.init();
</script>
-->

</html>
