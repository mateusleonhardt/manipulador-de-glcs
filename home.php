<?php
	$title = 'Gerador de Gramática';
	include("header.php");
?>
		
<?php 
	$montaFormalismo = "";
	$gramaticaValida = "";
	$tipoGramatica = "";
	$sentencasGeradas = "";
	$conjProducoes = "";

	$exGramatica = "S > aB|bC <br> B > bS|aBB|b <br> C > aS|bCC|a";
	$exGramatica2 = "S > aB|bC <br> B > bS|B|b <br> C > aS|bCC|a";
	$exGramatica3 = "S > aB|bC <br> B > bS|BAC|b <br> C > aS|bCC|&";
	$exGramatica4 = "S > aB|bC|& <br> B > bS|BAC|b <br> C > aS|bCC";
	
	if(isset($_POST['submit'])) 
	{
		// Variáveis com os valores digitados no formulário.
		$simbNaoTerminal = $_POST['simbNaoTerminal'];
		$simbTerminal = $_POST['simbTerminal'];
		$simbEnglobaConjunto = $_POST['simbEnglobaConjunto'];
		$simbEnblobaInicio = $_POST['simbEnblobaInicio'];
		$conjProducoes = $_POST['conjProducoes'];

		// Váriáveis com utilização de funções que se baseiam com o conteúdo do respectivo campo
		$simbNaoTerminalSemRepetidas = removeRepetidas($simbNaoTerminal);		
		$simbTerminalSemRepetidas = removeRepetidas($simbTerminal);		

		$erros = array();

		if (empty($simbNaoTerminal))
			$erros[] = 'Insira algum símbolo não terminal';

		if (empty($simbTerminal))
			$erros[] = 'Insira algum símbolo terminal';
		
		if (empty($simbEnglobaConjunto))
			$erros[] = 'Insira algum símbolo que engloba o conjunto das produções';

		if (empty($simbEnblobaInicio))
			$erros[] = 'Insira algum símbolo de início de produções';

		if (empty($conjProducoes))
			$erros[] = 'Insira algum conunto de produções';			

		if (validaSimbolosRepetidos($simbNaoTerminal) == true)
			$erros[] = 'Não é permitida a inserção de Simbolos Não Terminais repetidos';

		if (validaSimbolosRepetidos($simbTerminal) == true)
			$erros[] = 'Não é permitida a inserção de Simbolos Terminais repetidos';

		if (count($erros) > 0) 
		{
			echo "<div class='alert alert-danger' role='alert'>".implode('<br />', $erros)."</div>";
			echo "<div class='clearfix'></div>";
		}

		else
		{		
			$montaFormalismo = "Formalismo que representa a gramática:<br/>G = ({". $simbNaoTerminalSemRepetidas ."}, {". $simbTerminalSemRepetidas ."}, ". $simbEnglobaConjunto .",". $simbEnblobaInicio .")";
			$invalida = 'É uma gramática:' . '<br>' . 'Inválida';
			$valida = 'É uma gramática:' . '<br>' . "Válida";
			//Condições para dizer se é uma gramática válida ou inválida
			if(validaConjProdCamposGerais($conjProducoes, $simbTerminal, $simbNaoTerminal, $simbEnblobaInicio) === true)
				$gramaticaValida = $invalida;
			elseif (validaSimboloInicio($simbTerminal, $simbEnblobaInicio) === true)
				$gramaticaValida = $invalida;
			elseif (validaLadoEsqConjProd($conjProducoes) === true)
				$gramaticaValida = $invalida;			
			else
				$gramaticaValida = $valida;

			//Condições para verificar a qual gramática pertence 
			if (isGramaticaLivreContexto($conjProducoes)){ 
				$tipoGramatica = "É uma:";
				$tipoGramatica .= "<br>";		
				$tipoGramatica .= "Gramática Livre de Contexto";
			}
			else
			$gramaticaValida = $invalida;			

			// Gera as sentenças caso a gramática seja válida
			if ($gramaticaValida == $valida) {
				$conjSemSentencaVazia = removeSentencaVazia($conjProducoes);
				$conjSemSimbolosInuteis = eliminaSimbolosInuteis($conjSemSentencaVazia);
				$conjSemProducaoSimples = eliminaProducaoSimples($conjSemSimbolosInuteis);
				//$conjSemRecursaoAEsquerda = eliminaRecursaoAEsquerda($conjSemProducaoSimples);
				//$conjFatorado = fatoraConjunto($conjSemRecursaoAEsquerda);
				$sentencasGeradas = "Gramática transformada:";
				$sentencasGeradas .= "<br>";
				$sentencasGeradas .=  $conjSemProducaoSimples;
			}
		}
	}

	//Valida se há simbolos Terminar ou NT repetidos em seus respectivos campos
	function validaSimbolosRepetidos($string){
		$letras = str_split($string);
		$result = array();
		$previous = "";

		foreach ($letras as $letra) {
		 	if ($letra == $previous){
		 		$result = true;
		 	}
		 	$previous = $letra;
		}

		return $result;
	}

	// Remove repetidas para gerar o formalismo
	function removeRepetidas($string){
		$letras = str_split($string);
		$result = array();
		$previous = "";

		foreach ($letras as $letra) {
		 	if ($letra != $previous){
		 		$result[] = $letra;
		 	}
		 	$previous = $letra;
		}
		$str = implode(',',array_unique($result));

		return $str;
	}

	// Válida se o lado esquerdo do conjunto de produções possui mais de 1 simbolo
	function validaLadoEsqConjProd($conjProducoes){
		$lines = explode("\n", trim($conjProducoes));	
		foreach ($lines as $line) {
			$valoresCadaLinha[] = explode(">", $line);
		}
		
		$resultTrue = false;
		foreach ($valoresCadaLinha as $valor) {
			$tamanhoString = strlen(trim($valor[0]));
			if ($tamanhoString > 1){
				$resultTrue = true;
			}
			else{
				$resultFalse = false;			
			}
		}
		return $resultTrue;	

	}

	//Válida se o valor do simbolo de inicio EXISTE entre os simbolos terminais informados, se existir, retorna TRUE
	function validaSimboloInicio($simbolosT, $simbInicio){
		$pos = stripos($simbolosT, $simbInicio);
		if($pos === false)
			return false;
		else
			return true;
	}

	// Valida se os simbolos NT, T e de Inicio existem no Conjunto de produções
	function validaConjProdCamposGerais($conjProducao, $simbolosT, $simbolosNT, $simboloInicio){
		$result1 = "";
		$result2 = "";

		// Monta todos em uma string só
		$juntaSimbolos = $simbolosT . $simbolosNT . $simboloInicio;

		$removeQuebraDeLinha = str_replace('\n', "", $conjProducao); // Remove quebras de linha
		$removeSeparador = str_replace(">", "", $removeQuebraDeLinha); // Remove '>'
		$removeBarra = str_replace("|", "", $removeSeparador); // Remove as barras
		$removeEspacoBranco = $string = preg_replace('/\s+/', '', $removeBarra); // Remove todos os espaços em branco na string

		$letras = str_split($juntaSimbolos);
		foreach ($letras as $letra) {
			$posSimbT = stripos($removeEspacoBranco, $letra);
			if ($posSimbT === false){			
				$result1 = true;
			}
			else{
				$result2 = false;
			}
		}

		return $result1;		
	}

	function isMaiusculo($opcao)
	{
		return ctype_upper($opcao);
	}

	function isMinusculo($opcao)
	{
		if ($opcao == '&')
			return true;
		else
			return ctype_lower($opcao);
	}

	function primeiraMaiuscula($texto)
	{
		for ($i = 0; $i < strlen($texto); $i++) {
			if (isMaiusculo($texto[$i]))
				return $texto[$i];
		}

		return null;
	}

	function isGramaticaLivreContexto($conjProducoes){
		$lines = explode("\n", trim($conjProducoes));
		foreach ($lines as $line) {
			$valoresCadaLinha[] = explode(">", $line);		
		}

		$ehRegular = true;
		$naoRegular = false;
		foreach ($valoresCadaLinha as $valor) {
			$valoresLadoDireito = explode("|", $valor[1]);

			foreach ($valoresLadoDireito as $opcao) {
				$opcao = trim($opcao);
				$tamanhoString = strlen($opcao);

				if($tamanhoString == 0)
					return false;
			}	
		}

		return true;
	}

	function hasAlgumMaiusculo($arrayOpcoes)
	{
		foreach ($arrayOpcoes as $opcao) {
			if (isMaiusculo($opcao))
				return true;
		}

		return false;
	}

	/* 	- Esta função recebe o conjunto de produções como parâmetro.
			* Remove a sentença vazia do simbolo de inicio caso exista e cria um nova produção.
			* Remove a sentença vazia caso esteja em outra area do conjunto.
			* Não faz nada caso não encontre sentença vazia
		- Retorna um dos casos acima caso ocorra um deles.
	*/
	function removeSentencaVazia($conjProducoes)
	{
		$result = array();
		$lines = explode("\n", trim($conjProducoes));
		$primeiraProducao = strpos($lines[0], "&");

		if ($primeiraProducao !== false){
			$producaoQuebrada = explode(">", trim($lines[0]));
			$producaoQuebrada_1 = $producaoQuebrada[0];
			$producaoQuebrada_2 = $producaoQuebrada[1];
			$result[] = trim($producaoQuebrada_1) . "' > " . trim($producaoQuebrada_1) . "|&";
			$result[] = trim($producaoQuebrada_1) . " >" . str_replace("|&", "", $producaoQuebrada_2);
			
			$primLinha = $lines[0];
			foreach ($lines as $line) {
				if($line != $primLinha){
					$result[] = $line;
				}
			}

			return implode("\n", $result);
		}	

		else{
			$demaisProducoes = strpos($conjProducoes, "&");
			if($demaisProducoes !== false){
				$result = str_replace("|&", "", $lines);
				
				return implode("\n", $result);
			}
			else{
				$conjQuebrado = explode("\n", $conjProducoes);
				$result = implode("\n", $conjQuebrado);
				
				return $result;
			}
		}		
	}

	function eliminaSimbolosInuteis($conjSemSentencaVazia)
	{
		$lines = explode("\n", $conjSemSentencaVazia);
		$cada_simbolo_lado_direito = array();		
		foreach ($lines as $line) {
			$lineQuebrada = explode(">", $line);

			$simbolos_lado_esquerdo[] = trim($lineQuebrada[0]);

			$simbolos_lado_direito  = explode('|', trim($lineQuebrada[1]));
			foreach ($simbolos_lado_direito as $simbolo_d) {
				$cada_simbolo_lado_direito[] = trim($simbolo_d);
			}
				
		}
		
		$value = "";
		$previous = 0;
		$valor_n_existe = "";
		$simbolo_inicio = $simbolos_lado_esquerdo[0];
		foreach ($simbolos_lado_esquerdo as $simbolo_lado_esq) {
			if($simbolo_lado_esq != $simbolo_inicio){				
				foreach ($cada_simbolo_lado_direito as $simbolo_lado_dir) {
					$tamanho_simbolo = strlen(trim($simbolo_lado_dir));

					if($tamanho_simbolo == 1){
						$simb_esq_minusculo = strtolower($simbolo_lado_esq);
						
						if($simb_esq_minusculo != $simbolo_lado_dir){
							$value = $value . $simbolo_lado_dir;
						}
					}					
				}

				$t_antigo = strlen($value);
				if($t_antigo > $previous)
				{
					$valor_n_existe = $simbolo_lado_esq;
					
				}
				$previous = $t_antigo;
				$value = "";
			}			
		}

		$f = array();
		$h = array();
		$h = "";
		foreach ($lines as $line) {
			$valor_inutil = strpos($line, $valor_n_existe);
			if($valor_inutil !== false){
				$f[] = str_ireplace($valor_n_existe, "", $line);			
			}
			else
			{			
				$h[] = $line;
			}
		}

		if(count($f) != 0){
			$final_result = implode("\n", $h) . "\n" . implode("\n", $f);
			return $final_result;
		}
		else
		{
			return $conjSemSentencaVazia;
		}
	}

	function eliminaProducaoSimples($conjSemSentencaVazia)
	{
		$lines = explode("\n", $conjSemSentencaVazia);
		$cada_simbolo_lado_direito = array();		
		foreach ($lines as $line) {

			$lineQuebrada = explode(">", $line);
			if(trim($lineQuebrada[0]) != ""){
				$simbolos_lado_esquerdo[] = trim($lineQuebrada[0]);
			}

			$simbolos_lado_direito  = explode('|', trim($lineQuebrada[1]));
			foreach ($simbolos_lado_direito as $simbolo_d) {				
				if($simbolo_d != ""){
					$cada_simbolo_lado_direito[] = trim($simbolo_d);
				}
			}
				
		}
		
		$value = "";
		$simbolo_inicio = $simbolos_lado_esquerdo[0];
		foreach ($simbolos_lado_esquerdo as $simbolo_lado_esq) {
			if($simbolo_lado_esq != $simbolo_inicio){				
				foreach ($cada_simbolo_lado_direito as $simbolo_lado_dir) {
					$tamanho_simbolo = strlen(trim($simbolo_lado_dir));

					if($tamanho_simbolo == 1){
						if($simbolo_lado_esq == $simbolo_lado_dir){
							$value = trim($simbolo_lado_dir);
						}
					}					
				}
			}			
		}

		$valores_lado_direito = "";
		foreach ($lines as $line) {
			$lineQuebrada = explode(">", $line);
			if($line{0} == $value){
				$linha_toda = trim($lineQuebrada[1]);

				if ($linha_toda{strlen($linha_toda)-1} == "|"){
					$valores_lado_direito = str_replace("|", "", $linha_toda);
				}
			}
		}

		$value = "";
		$tudo = array();
		$simbolo_inicio = $simbolos_lado_esquerdo[0];
		foreach ($simbolos_lado_esquerdo as $simbolo_lado_esq) {
			if($simbolo_lado_esq != $simbolo_inicio){				
				foreach ($cada_simbolo_lado_direito as $simbolo_lado_dir) {
					$tamanho_simbolo = strlen(trim($simbolo_lado_dir));

					if($tamanho_simbolo == 1){
						if($simbolo_lado_esq == $simbolo_lado_dir){
							$simbolo_lado_dir = $valores_lado_direito;
							$tudo[] = $simbolo_lado_dir;

						}
					}
					else{
						$tudo[] = $simbolo_lado_dir;
					}								
				}
			}			
		}

		return  "Valores lado esquerdo: " . implode(",", $simbolos_lado_esquerdo) . "<br>" . "Valores lado direito: " . implode("|", $tudo);
	}

?>
	<div class="row">
		<div class="col-xs-12 col-sm-7 col-md-6">
			<form action="" method="post" enctype="multipart/form-data" role="form">
				<div class="form-group">					
					<div class='row'>
						<div class="col-xs-12 col-sm-7 col-md-11">
							<label class="control-label"> Símbolos não terminais: </label>
							<input name="simbNaoTerminal" class="form-control" type="text" id="simbNaoTerminal" pattern="[A-Za-z]*"  placeholder="Exemplo: ABC"/> 
						</div>
					</div>
				</div>	
				<div class="form-group">					
					<div class='row'>
						<div class="col-xs-12 col-sm-7 col-md-11">
							<label class="control-label"> Símbolos terminais: </label>
							<input name="simbTerminal" class="form-control" type="text" id="simbTerminal" pattern="[A-Za-z]*"  placeholder="Exemplo: abc"/>						
						</div>
					</div>
				</div>
				<div class="form-group">					
					<div class='row'>
						<div class="col-xs-12 col-sm-7 col-md-11">
							<label class="control-label"> Símbolo que engloba o conjunto de produções: </label>
							<input name="simbEnglobaConjunto" class="form-control" type="text" id="simbEnglobaConjunto" pattern="[A-Za-z]{1}"  title="É permitido apenas uma letra." maxlength="1"  placeholder="Exemplo: P"//>
						</div>
					</div>
				</div>
				<div class="form-group">					
					<div class='row'>
						<div class="col-xs-12 col-sm-7 col-md-11">
							<label class="control-label"> Símbolo de inicio de produções: </label>
							<input name="simbEnblobaInicio" class="form-control" type="text" id="simbEnblobaInicio" pattern="[A-Za-z]{1}" title="É permitido apenas uma letra." maxlength="1"  placeholder="Exemplo: S"//>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class='row'>
						<div class="col-xs-12 col-sm-7 col-md-11">
							<label class="control-label"> Conjunto de produções que engloba a gramática: </label>
							<textarea name="conjProducoes" class="form-control" type="text" id="conjProducoes" placeholder="Exemplos: ao lado"></textarea>
							<label class="control-label alert alert-warning"> Representaremos a sentença vazia com o simbolo "&" </label> 
						</div>
					</div>
				</div>
				<div class="form-group">
					<input class="btn btn-primary" type="submit" name="submit" value="Transformar"/>
				</div>
				<div class="growRow"></div>
			</form>
		</div>
		<div class="col-xs-12 col-sm-5 col-md-6">	
			<div class="panel panel-warning">
	  			<div class="panel-heading">Observação:</div>
				<div class="panel-body">
					<p>
						<p class="text-primary">Regras de uma GLC:</p>
						<span class="control-label"><strong>Lado esquerdo:</strong> sempre ocorrer um e apenas 1 não-terminal</span><br>
						<span class="control-label"><strong>Lado direito:</strong> qualquer conjunto de sentenças, inclusive a sentença vazia (E - livre)</span>
					</p>

					<p class="text-primary">Exemplos de GLC:</p>
					<table class="table">
						<tr>
							<td><span class="control-label"><?= $exGramatica ?></span></td>
							<td><span class="control-label"><?= $exGramatica2 ?></span></td>
							<td><span class="control-label"><?= $exGramatica3 ?></span></td>
							<td><span class="control-label"><?= $exGramatica4 ?></span></td>
						</tr>
					</table>

					<div <?= ($conjProducoes == "" ? "hidden" : "")?>>
						<hr>
						<p class="text-primary">GLC utilizada:</p>
						<div><?= $conjProducoes ?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-5 col-md-6">	
			<div class="panel panel-primary break-word">
	  			<div class="panel-heading">Resultado</div>
				<div class="panel-body">
					<p <?= ($montaFormalismo == "" ? "hidden" : "")?>>
						<label class="control-label"><?= $montaFormalismo ?></label>
					</p>
					<p <?= ($gramaticaValida == "" ? "hidden" : "")?>>
						<label class="control-label"><?= $gramaticaValida ?></label>
					</p>
					<p <?= ($tipoGramatica == "" ? "hidden" : "")?>>
						<label class="control-label"><?= $tipoGramatica ?></label>
					</p>
					<p <?= ($sentencasGeradas == "" ? "hidden" : "")?>>
						<label class="control-label"><?= $sentencasGeradas ?></label>
					</p>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('#simbNaoTerminal').keyup(function(){
			this.value = this.value.toUpperCase();
		});
		$('#simbEnglobaConjunto').keyup(function(){
			this.value = this.value.toUpperCase();
		});
		$('#simbEnblobaInicio').keyup(function(){
			this.value = this.value.toUpperCase();
		});
		$('#simbTerminal').keyup(function(){
			this.value = this.value.toLowerCase();
		});
	</script>	
<?php
	include("footer.php");
?>