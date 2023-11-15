<?php
include 'controleDeAcesso.php';
require_once 'conexao.php';

$id_usuario = $_SESSION['id_usuario'];
$id_nucleo = $_SESSION['id_nucleo'];

$qr_entrada = $bd->query("SELECT SUM(valor_financa) as entrada from tb_financas where tipo_financa = 1 and pago = 1 and id_nucleo = 1");
$qr_entrada->execute();
$total_entrada = $qr_entrada->fetch(PDO::FETCH_ASSOC);

$qr_saida = $bd->query("SELECT SUM(valor_financa) as saida from tb_financas where tipo_financa = 0 and pago = 1 and id_nucleo = 1");
$qr_saida->execute();
$total_saida = $qr_saida->fetch(PDO::FETCH_ASSOC);
$saldo = $total_entrada['entrada'] - $total_saida['saida'];

$qr_financas_ok = $bd->query("SELECT * FROM tb_financas where id_nucleo = 1");

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciae</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <script src="https://kit.fontawesome.com/e80d3685eb.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include('header.php');
  ?>
  <main>
    <div class="container pt-4 custom-hide-sm">
      <button type="button" class="btn btn-success mt-5 nova_matraca" data-bs-toggle="modal" data-bs-target="#nova_matraca">
        Novo
      </button>
      <button type="button" class="btn btn-warning mt-5 " data-bs-toggle="modal" data-bs-target="#importar_financa">
        Importar
      </button>
      <div class="row row-cols-3 row-cols-md-3 g-4 pt-4 mb-4">
        <div class="col">
          <div class="card bg-light border-success">
            <div class="card-header border-success bg-success text-light">Entrada</div>
            <div class="card-body border-success  text-center">
              <h5 class="card-title text-success"><?php echo number_format($total_entrada['entrada'], 2, ",", ".") ?></h5>
            </div>
          </div>
          <div class="card-footer border-success bg-success text-light"></div>
        </div>

        <div class="col">
          <div class="card bg-light border-danger">
            <div class="card-header border-danger bg-danger text-light">Saida</div>
            <div class="card-body border-danger  text-center">
              <h5 class="card-title text-danger"><?php echo number_format($total_saida['saida'], 2, ",", ".") ?></h5>
            </div>
          </div>
          <div class="card-footer border-danger bg-danger text-light"></div>
        </div>

        <div class="col">
          <div class="card bg-light border-primary">
            <div class="card-header border-primary bg-primary text-light">Saldo</div>
            <div class="card-body border-primary  text-center">
              <h5 class="card-title text-primary"><?php echo number_format($saldo, 2, ",", ".") ?></h5>
            </div>
          </div>
          <div class="card-footer border-primary bg-primary text-light"></div>
        </div>
      </div>
    </div>
    <hr>
    <div class="container mt-4 mb-4">
      <div class="row">
        <h3>Fluxo de Caixa</h3>
      </div>
      <div class="row row-cols-1 row-cols-md-1 g-4 pt-4 mb-4">
        <div class="col">
          <div class="card bg-light border-dark overflow-auto" style="height: 600px;">

            <div class="card-body border-dark p-0">
              <table class="table table-borderless text-center matracas">
                <thead>
                  <tr class="">
                    <th class="custom-hide-sm">Usuario</th>
                    <th class="col-2">Descrição</th>
                    <th class="col-2">Valor</th>
                    <th class="custom-hide-sm">Data</th>
                    <th class="custom-hide-sm">Tipo</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody class="align-middle">
                  <?php while ($cada_financa = $qr_financas_ok->fetch()) {
                    if (empty($cada_financa['id_dirigente']) || empty($cada_financa['id_cursista'])) {
                      $usuario = "-";
                    } else {
                      if (empty($cada_financa['id_dirigente'])) {
                        $usuario = $cada_financa['id_cursista'];
                      } else {
                        $usuario = $cada_financa['id_dirigente'];
                      }
                    }
                    if ($cada_financa['tipo_financa'] == 1) {
                      $tipo_financa = "Entrada";
                    } else {
                      $tipo_financa = "Saída";
                    }
                  ?>

                    <tr class="">
                      <td class="custom-hide-sm"><?php echo $usuario ?></td>
                      <td><?php echo $cada_financa['descricao_financa'] ?></td>
                      <td><?php echo number_format($cada_financa['valor_financa'], 2, ",", ".") ?></td>
                      <td class="custom-hide-sm"><?php echo date('d/m/y', strtotime($cada_financa['data_financa'])); ?></td>
                      <td class="custom-hide-sm"><?php echo $tipo_financa ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-warning rounded-circle text-white abrir-modal" data-bs-toggle="modal" data-bs-target="#modal_edit_financa" data-id-financa="<?php echo $cada_financa['id_financa'] ?>">
                          <i class="fa-solid fa-circle-info"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary rounded-circle modal_ver_matraca_tlc" data-bs-toggle="modal" data-bs-target="#modal_ver_matraca_tlc" data-teste=""><i class="fa-solid fa-pen"></i></button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>

              <table class="table table-borderless text-center financas" style="display: none;">
                <thead>
                  <tr class="">
                    <th>Nome</th>
                    <th>Função</th>
                    <th>Saldo</th>
                  </tr>
                </thead>
                <tbody class="align-middle">
                  <tr class="tipo-">

                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success rounded-circle modal_usuario_comprar" data-bs-toggle="modal" data-bs-target="#modal_usuario_comprar" data-teste=""><i class="fa-solid fa-money-bills"></i></button>
                      <button type="button" class="btn btn-sm btn-warning rounded-circle modal_edit_funcao_tlc" data-bs-toggle="modal" data-bs-target="#modal_edit_funcao_tlc" data-teste=""><i class="fa-solid fa-pen"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer border-dark">
              <div class="row">
                <div class="col-9">
                </div>
                <div class="col-3">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nova Matraca-->
    <div class="modal fade" id="nova_matraca" tabindex="-1" aria-labelledby="nova_matracaLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="nova_matracaLabel">Nova Matraca</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <form class="row g-3" method="$_POST" name="form_nova_matraca" id="form_nova_matraca">
                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="select_nova_matraca" class="form-label">Matraca</label>
                    <select class="form-select" id="select_nova_matraca" name="select_nova_matraca">
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="input_pregador_nova_matraca" class="form-label">Pregador</label>
                    <input type="text" class="form-control" id="input_pregador_nova_matraca" aria-describedby="input_pregador_nova_matraca" name="input_pregador_nova_matraca">
                  </div>
                </div>

                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_novo_telefone_pregador" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="input_novo_telefone_pregador" aria-describedby="input_novo_telefone_pregador" name="input_novo_telefone_pregador">
                  </div>
                  <div class="col-md-6">
                    <label for="input_novo_responsavel" class="form-label">Responsável</label>
                    <select class="form-select" id="input_novo_responsavel" name="input_novo_responsavel">
                    </select>
                  </div>
                </div>

                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_nova_data_ini_matraca" class="form-label">Data da Matraca</label>
                    <input type="date" class="form-control" id="input_nova_data_ini_matraca" aria-describedby="input_nova_data_ini_matraca" name="input_nova_data_ini_matraca">
                  </div>
                  <div class="col-md-6">
                    <label for="input_novo_tempo_matraca" class="form-label">Tempo da Matraca</label>
                    <input type="time" class="form-control" id="input_novo_tempo_matraca" aria-describedby="input_novo_tempo_matraca" name="input_novo_tempo_matraca">
                    <label for="" id="tempo_novo_orientado">Tempo orientado </label>
                  </div>
                </div>
                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_nova_ajuda_de_custo" class="form-label">Ajuda de Custo</label>
                    <input type="number" min=0 class="form-control" id="input_nova_ajuda_de_custo" aria-describedby="input_nova_ajuda_de_custo" name="input_nova_ajuda_de_custo">
                  </div>
                </div>
                <div class="col-12 border-top p-3">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                  <button type="submit" class="btn btn-primary" id="btn_form_nova_matraca">Salvar alterações</button>
                </div>
                <input type="hidden" name="input_nova_id_tlc" id="input_nova_id_tlc">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="importar_financa" tabindex="-1" aria-labelledby="importar_financaLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="importar_financaLabel">Nova Importação <i class="fa-solid fa-eye exemplo-importacao" style="cursor:pointer;"></i></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <form class="row g-3" method="$_POST" name="form_importar_financa" id="form_importar_financa">
                <div class="mb-2 row">
                  <div class="col-md-12 ">
                    <table class="table-exemple" style="display:none">
                      <thead>
                        <tr>
                          <th>Id</th>
                          <th>Tipo</th>
                          <th>Nome</th>
                          <th>Descrição</th>
                          <th>Data do Pgto</th>
                          <th>Forma de Pagto</th>
                          <th>Valor (R$)</th>
                          <th>Entrada/Saída</th>
                          <th>Pago</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>Cursista</td>
                          <td>Fernando Alves</td>
                          <td>Reembolso Fernando</td>
                          <td>11/10/2023</td>
                          <td>PIX</td>
                          <td>150</td>
                          <td>Entrada</td>
                          <td>Ok</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="mb-2 row">
                  <div class="col-md-12 mt-2">
                    <label for="formFile" class="form-label">Escolha o arquivo CSV</label>
                    <input class="form-control" type="file" id="formFile">
                  </div>
                </div>
                <div class="col-12 border-top p-3">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                  <button type="submit" class="btn btn-primary" id="btn_form_nova_importacao">Salvar alterações</button>
                </div>
                <input type="hidden" name="id_usuario_importacao" id="id_usuario_importacao">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal Editar Matraca-->
    <div class="modal fade" id="modal_edit_financa" tabindex="-1" aria-labelledby="modal_edit_financaLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal_edit_financaLabel">Dados da Finança</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <form class="row g-3" method="$_POST" name="form_edit_financa" id="form_edit_financa">
                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_edit_nome_usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="input_edit_nome_usuario" aria-describedby="input_edit_nome_usuario" name="input_edit_nome_usuario" disabled>
                  </div>
                  <div class="col-md-6">
                    <label for="input_edit_nome_cursista" class="form-label">Dirigente/Cursista</label>
                    <input type="text" class="form-control" id="input_edit_nome_cursista" aria-describedby="input_edit_nome_cursista" name="input_edit_nome_cursista" disabled>
                  </div>
                </div>

                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_edit_tipo_financa" class="form-label">Tipo</label>
                    <select class="form-select" id="input_edit_tipo_financa">
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="input_edit_forma_pgto" class="form-label">Forma de Pagamento</label>
                    <select class="form-select" id="input_edit_forma_pgto">
                    </select>
                  </div>
                </div>

                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_edit_valor" class="form-label">Valor (R$)</label>
                    <input type="number" min=0 class="form-control" step="0.01" id="input_edit_valor" aria-describedby="input_edit_valor" name="input_edit_valor">
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="new_tipo_1" name="input_new_pago" class="custom-control-input" value="1" checked>
                      <label class="custom-control-label" for="new_tipo_1">Pago</label>
                      <input type="radio" id="new_tipo_0" name="input_new_pago" class="custom-control-input" value="0">
                      <label class="custom-control-label" for="new_tipo_0">Não pago</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label for="input_edit_data" class="form-label">Data</label>
                    <input type="date" class="form-control" id="input_edit_data" aria-describedby="input_edit_data" name="input_edit_data">
                  </div>

                </div>
                <div class="mb-2 row">
                  <div class="col-md-12">
                    <label for="input_edit_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" name="input_edit_descricao" id="" cols="30" rows="5"></textarea>
                  </div>
                </div>
                <div class="col-12 border-top p-3">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                  <button type="submit" class="btn btn-primary" id="btnform_edit_financa">Salvar alterações</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Usuario-->
    <div class="modal fade" id="modal_edit_funcao_tlc" tabindex="-1" aria-labelledby="modal_edit_funcao_tlcLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal_edit_funcao_tlcLabel">Dados do Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <form class="row g-3" method="$_POST" name="form_edit_usuario" id="form_edit_usuario">
                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_edit_nome_usuario" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="input_edit_nome_usuario" aria-describedby="input_edit_nome_usuario" name="input_edit_nome_usuario" disabled>
                  </div>
                  <div class="col-md-6">
                    <label for="input_edit_funcao_usuario" class="form-label">Função</label>
                    <select class="form-select" id="input_edit_funcao_usuario" name="input_edit_funcao_usuario">
                    </select>
                    <input type="hidden" id="input_edit_id_funcao_usuario" name="input_edit_id_funcao_usuario">
                  </div>
                </div>
                <div class="col-12 border-top p-3">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                  <button type="submit" class="btn btn-primary" id="btnform_edit_usuario">Salvar alterações</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Usuario Comprar-->
    <div class="modal fade" id="modal_usuario_comprar" tabindex="-1" aria-labelledby="modal_usuario_comprarLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal_usuario_comprarLabel">Dados do Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <form class="row g-3" method="$_POST" name="form_usuario_comprar" id="form_usuario_comprar">
                <div class="mb-2 row">
                  <div class="col-md-6">
                    <label for="input_nome_usuario_comprar" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="input_nome_usuario_comprar" aria-describedby="input_nome_usuario_comprar" name="input_nome_usuario_comprar" disabled>
                  </div>
                  <div class="col-md-6">
                    <label for="input_usuario_acao_comprar" class="form-label">Ação</label>
                    <select class="form-select" id="input_usuario_acao_comprar" name="input_usuario_acao_comprar">
                      <option value="0">Comprar</option>
                      <option value="1">Pagar</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="input_usuario_valor_comprar" class="form-label">Valor</label>
                    <input type="number" class="form-control" id="input_usuario_valor_comprar" aria-describedby="input_usuario_valor_comprar" name="input_usuario_valor_comprar">
                  </div>
                  <div class="col-md-6 desc_compra">
                    <label for="input_usuario_desc_comprar" class="form-label">Descrição da Compra</label>
                    <input type="text" class="form-control" id="input_usuario_desc_comprar" aria-describedby="input_usuario_desc_comprar" name="input_usuario_desc_comprar">
                  </div>
                  <div class="col-md-6 forma_pgto" style="display: none;">
                    <label for="input_usuario_forma_comprar" class="form-label">Forma de Pagamento</label>
                    <select class="form-select" id="input_usuario_forma_comprar" name="input_usuario_forma_comprar">
                    </select>
                  </div>
                </div>
                <div class="col-12 border-top p-3">
                  <input type="hidden" class="input_id_usuario_comprar" class="input_id_usuario_comprar" name="input_id_usuario_comprar">
                  <input type="hidden" class="input_id_tlc_comprar" class="input_id_tlc_comprar" name="input_id_tlc_comprar">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                  <button type="submit" class="btn btn-primary" id="btnform_edit_usuario">Salvar alterações</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <div class="container-fluid">
    <nav class="navbar fixed-bottom bottom-menu d-sm-none">
      <div class="container col-12 text-center">
        <div class="dropdown col-4" data-bs-toggle="offcanvas" data-bs-target="#dasboard_menu" aria-controls="dasboard_menu">
          <i class="fa-solid fa-bars-staggered" data-toggle="dropdown"></i>
        </div>

        <div class="dropdown col-4" data-bs-toggle="offcanvas" data-bs-target="#dasboard_centro" aria-controls="dasboard_centro">
          <i class="fas fa-home icon" data-toggle="dropdown"></i>
        </div>

        <div class="dropdown col-4" data-bs-toggle="offcanvas" data-bs-target="#dasboard_direita" aria-controls="dasboard_direita">
          <i class="fa-solid fa-eye" data-toggle="dropdown"></i>
        </div>
      </div>
    </nav>
  </div>

  <!-- DASHBOARD MENU-->
  <div class="offcanvas offcanvas-start d-flex flex-column flex-shrink-0 p-3 bg-dark" tabindex="-1" id="dasboard_menu" aria-labelledby="dasboard_menuLabel" style="width: 60%;">

    <a href="" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
      <img src="https://via.placeholder.com/150x150/bdbac1?text=GERENCIAE" alt="" width="50" height="50" class="rounded-circle me-4">
      <span class="fs-4 text-white">Bem vindo</span>
    </a>
    <hr class="text-white">
    <ul class="nav nav-pills flex-column mb-auto menus">
      <li class="nav-item">
        <a href="index.php" class="nav-link text-white" aria-current="page">
          <i class="fa-solid fa-house"></i>
          Inicio
        </a>
      </li>
      <li>
        <a href="dashboard.php" class="nav-link text-white">
          <i class="fa-solid fa-gauge-high"></i>
          Dashboard
        </a>
      </li>
      <li>
        <a href="tlc.php" class="nav-link text-white">
          <i class="fa-solid fa-timeline"></i>
          TLC
        </a>
      </li>
      <li>
        <a href="meu_nucleo.php" class="nav-link text-white">
          <i class="fa-solid fa-church"></i>
          Meu Nucleo
        </a>
      </li>
      <?php if (($meu_usuario['id_tipo'] == 2) || ($meu_usuario['id_tipo'] == 5)) { ?>
        <li>
          <a href="financeiro_nucleo.php" class="nav-link text-white">
            <i class="fa-solid fa-coins"></i>
            Financeiro
          </a>
        </li>
      <?php } ?>
      <li>
        <a href="matraca.php" class="nav-link text-white">
          <i class="fa-solid fa-clipboard"></i>
          Matracas
        </a>
      </li>
    </ul>
    <hr class="text-white">
    <div class="dropdown">
      <a href="" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://via.placeholder.com/150x150/bdbac1?text=GERENCIAE" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong><?php echo $meu_usuario['nome_usuario'] ?></strong>
      </a>
      <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
        <li><a class="dropdown-item" href="#">Configurações</a></li>
        <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item" href="logout.php">Sair</a></li>
      </ul>
    </div>
  </div>

  <!-- DASHBOARD DIREITA-->
  <div class="offcanvas offcanvas-end d-flex flex-column flex-shrink-0 p-3 bg-dark " tabindex="-1" id="dasboard_direita" aria-labelledby="dasboard_direitaLabel" style="width: 60%;">

    <div class="container mb-0">
      <div class="row">
        <button type="button" class="btn btn-success nova_matraca" data-bs-toggle="modal" data-bs-target="#nova_matraca">
          Nova Matraca
        </button>
      </div>
      <div class="row">
        <button type="button" class="btn btn-warning mt-2 atualizar">
          Atualizar
        </button>
      </div>
      <div class="row">
        <button type="button" class="btn btn-danger mt-2 financeiro">
          Finanças
        </button>
        <button type="button" class="btn btn-danger mt-2 financeiro">
          Entrada/Saída
        </button>
      </div>
    </div>
  </div>
</body>

<script>
  $('.abrir-modal').on('click', function() {
    var id_financa = $(this).data('id-financa');
    $.ajax({
      url: 'buscar_financa.php',
      method: 'POST',
      data: {
        id_financa: id_financa,
      },
      success: function(data) {
        alert("foi");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error('Erro na solicitação AJAX:', textStatus, errorThrown);
        console.log('Detalhes completos do objeto jqXHR:', jqXHR);
      }
    });
  });

  function confirmaMatraca(id_matraca, id_tlc) {
    $.ajax({
      url: 'dashboard/confirmaMatraca.php',
      type: 'POST',
      data: {
        id_matraca: id_matraca,
        id_tlc: id_tlc
      },
      dataType: 'json',
      success: function(response) {
        alert('Erro ao buscar informações da matraca.');
      },
      error: function() {
        alert('Matraca Concluida!');
        location.reload();
      }
    });
  }
  $(document).ready(function() {
    $(".exemplo-importacao").click(function(e) {
      console.log("console");
      $(".table-exemple").toggle();
    })

    $("#select_nova_matraca").change(function() {
      var valorInput = $(this).val();

      $.ajax({
        url: 'dashboard/busca_tempo.php',
        method: 'POST',
        data: {
          id_matraca: valorInput
        }, // Envie o valor como dados da requisição
        success: function(response) {
          // Limpa todas as opções existentes no select
          $('#tempo_novo_orientado').empty();

          // Adiciona as opções retornadas pela requisição AJAX
          $('#tempo_novo_orientado').append(response);
        },
        error: function(error) {
          console.error('Erro na requisição: ' + error);
        }
      });
    })

    $("#form_nova_matraca").submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'dashboard/nova_matraca_tlc.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
          console.log(response);
          alert('Matraca Cadastrada!');
          location.reload();
        }
      });
    });

    $("#form_edit_financa").submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'dashboard/altera_matraca_tlc.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
          console.log(response);
          alert("");
          location.reload();
        }
      });
    });

    $("#input_edit_responsavel_matraca").change(function() {
      console.log($(this).val());
      var valor = $(this).val();
      $("#input_edit_resp_matraca").val(valor);
    });

    $("#form_edit_usuario").submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'dashboard/altera_usuario_tlc.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
          console.log(response);
          alert("");
          location.reload();
        }
      });
    });

    $("#form_usuario_comprar").submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'dashboard/compra_usuario_tlc.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
          console.log(response);
          alert("");
          location.reload();
        }
      });
    });

    $('#modal_usuario_comprar').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id_funcao_tlc = button.data('teste');
      $.ajax({
        url: 'dashboard/buscar_usuario_tlc.php',
        method: 'POST',
        data: {
          id_funcao_tlc: id_funcao_tlc,
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          $('#input_nome_usuario_comprar').val(data.nome_usuario);
          $('#input_id_usuario_comprar').val(data.id_usuario);
          $('#input_id_tlc_comprar').val(data.id_tlc);
        },
        error: function() {
          console.log("erro");
          alert('Erro ao buscar informações da matraca.');
        }
      });
    });

    $('#input_usuario_acao_comprar').change(function(e) {
      valor = $(this).val();
      console.log(valor);
      if (valor == 1) {
        $('.forma_pgto').show();
        $('.desc_compra').hide();
      } else {
        $('.forma_pgto').hide();
        $('.desc_compra').show();
      }
    });



    $(".simplificar").click(function() {
      $(".vazio").toggle();
      $(".cursista").toggle();
    });

    $(".financeiro").click(function() {
      $(".matracas, .financas").toggle();
    });
  });
</script>

</html>