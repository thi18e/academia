<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Academia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

  function carousel() {
    echo <<<HTML
<header class="position-relative">
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="academia1.png" class="d-block w-100" alt="Imagem 1" style="height: 250px; object-fit: cover;">
      </div>
      <div class="carousel-item">
        <img src="academia2.png" class="d-block w-100" alt="Imagem 2" style="height: 250px; object-fit: cover;">
      </div>
      <div class="carousel-item">
        <img src="academia3.png" class="d-block w-100" alt="Imagem 3" style="height: 250px; object-fit: cover;">
      </div>
    </div>
    <div class="carousel-caption fixed-center d-none d-md-block">
      <h1 class="text-white">Bem-vindo à Academia</h1>
      <p>Transforme seu corpo. Transforme sua vida.</p>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Próximo</span>
    </button>
  </div>
</header>
HTML;
  }

  <!-- Navbar será carregada por JavaScript -->
  <?php include '../includes/navbar.php'; ?>

  
  </header>


  <!-- Agendar Experimental -->
<section id="agendar" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Agende aqui!</h2>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header">Spinning</div>
                    <div class="card-body">
                        <p class="card-text">Experimente uma aula de spinning e conheça a nossa energia!</p>
                        <a href="<?= $paginaDestino ?>" class="btn btn-primary">Agendar Spinning</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="card text-center">
                    <div class="card-header">Musculação</div>
                    <div class="card-body">
                        <p class="card-text">Venha experimentar a nossa área de musculação com equipamentos modernos.</p>
                        <a href="<?= $paginaDestino ?>" class="btn btn-primary">Agendar Musculação</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="card text-center">
                    <div class="card-header">Avaliação Física</div>
                    <div class="card-body">
                        <p class="card-text">Agende uma avaliação física para começar seu plano personalizado.</p>
                        <a href="<?= $paginaDestino ?>" class="btn btn-primary">Agendar Avaliação</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

  <!-- Sobre -->
  <section id="sobre" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Sobre Nós</h2>
      <p class="text-center">Somos uma academia completa com equipamentos modernos, instrutores qualificados e planos acessíveis para todos os objetivos.</p>
    </div>
  </section>

  <!-- Planos -->
  <section id="planos" class="bg-light py-5">
    <div class="container">
      <h2 class="text-center mb-4">Conheça nossos planos!</h2>
      <div class="row justify-content-center">
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-header">Básico</div>
            <div class="card-body">
              <h5 class="card-title">R$ 49,90 / mês</h5>
              <p class="card-text">Acesso livre à academia durante o horário comercial + aula de spinning + musculação.</p>
              <a href="#" class="btn btn-primary">Assinar</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mt-3 mt-md-0">
          <div class="card text-center">
            <div class="card-header">Premium</div>
            <div class="card-body">
              <h5 class="card-title">R$ 79,90 / mês</h5>
              <p class="card-text">Acesso 24h + aulas em grupo + avaliação física mensal.</p>
              <a href="#" class="btn btn-primary">Assinar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Galeria de Fotos -->
  <section id="estrutura" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Nossa Estrutura</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <img src="../assets/img/musculacao.jpeg" class="img-fluid rounded shadow img-galeria" alt="Área de musculação">
          <p class="text-center mt-2">Área de Musculação</p>
        </div>
        <div class="col-md-4">
          <img src="../assets/img/aula.jpeg" class="img-fluid rounded shadow img-galeria" alt="Sala de spinning">
          <p class="text-center mt-2">Aula de Spinning</p>
        </div>
        <div class="col-md-4">
          <img src="../assets/img/recepecao.png" class="img-fluid rounded shadow img-galeria" alt="Recepção da academia">
          <p class="text-center mt-2">Recepção da Academia</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contato -->
  <section id="contato" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Fale Conosco</h2>
      <form class="row g-3 justify-content-center">
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Seu nome" required>
        </div>
        <div class="col-md-6">
          <input type="email" class="form-control" placeholder="Seu email" required>
        </div>
        <div class="col-md-12">
          <textarea class="form-control" rows="4" placeholder="Sua mensagem" required></textarea>
        </div>
        <div class="col-md-4 text-center">
          <button type="submit" class="btn btn-success">Enviar</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Rodapé -->
  <?php include '../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>