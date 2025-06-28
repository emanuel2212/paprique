
<body class="js">
	<!-- Shop Login -->
	<section class="shop login section">
		<div class="container">
			<div class="row">	
				<div class="col-lg-6 offset-lg-3 col-12">
					<div class="login-form">
						<h2>Login</h2>
						<p>Por favor, registe-se para finalizar a compra mais rapidamente</p>

						<style>
							.alert-error {
								color: #721c24;
								background-color: #f8d7da;
								border-color: #f5c6cb;
								padding: 10px;
								margin-bottom: 15px;
								border-radius: 4px;
								text-align: center;
							}
						</style>


						<!-- Mensagens de erro -->
						<?php if (isset($_SESSION['erro_login'])): ?>
							<div class="alert-error">
								<?php
								echo $_SESSION['erro_login'];
								unset($_SESSION['erro_login']); // Limpa a mensagem após exibir
								?>
							</div>
						<?php endif; ?>



						<!-- Form -->
						<form class="form" method="post" action="./dashboard/bd/login.php">
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<label>Nome de Utilizador<span>*</span></label>
										<input type="text" name="username" placeholder="Nome do Utilizador" required>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<label>Palavra-passe<span>*</span></label>
										<input type="password" name="password" placeholder="Coloque sua Palavra-passe" required>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group login-btn">
										<button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
										<p class="mt-5 mb-3 text-muted">&copy; 2025</p>
									</div>
									

									<a href="?page=register" class="btn">Registar</a>
								</div>
							</div>
						</form>
						<!--/ End Form -->
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/ End Login -->
</body>

</html>