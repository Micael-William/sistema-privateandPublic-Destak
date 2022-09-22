<?php
	require_once("lib/DataValidator.php");
	require_once("models/JornalModel.php");

	$nome = $_REQUEST['nome_jornal'];
	$status = $_REQUEST['status_jornal'];		
	$secretaria_id = $_REQUEST['secretaria_id'];		
	//$cidade = $_REQUEST['cidade_jornal'];	
	$retorno = null;
	
	if( isset($nome) && 
		!DataValidator::isEmpty($nome) && 
		isset($status) && 
		!DataValidator::isEmpty($status) && 
		isset($secretaria_id) && 
		!DataValidator::isEmpty($secretaria_id) 
	){
			
		try{
			$jornal_cadastrado = JornalModel::getByNome($nome);	
			
			if( DataValidator::isEmpty($jornal_cadastrado) ){	
			
				$jornal = new Jornal();
				$jornal->setNome( isset($nome) && !DataValidator::isEmpty($nome) ? $nome : null );
				$jornal->setStatus( isset($status) && !DataValidator::isEmpty($status) ? $status : null );
				$jornal->setAtivo( 'A' );
				
				$endereco = new Endereco();
				//$endereco->setCidade( isset($cidade) && !DataValidator::isEmpty($cidade) ? $cidade : null );
				$jornal->setEndereco( $endereco );
					
				$secretaria = new Secretaria();
				$secretaria->setId( $secretaria_id );
				$jornal->setSecretaria( $secretaria );	
				
				$usuario = new Usuario();
				$usuario->setId( $_REQUEST['usuario_id'] );
				$jornal->setUsuario( $usuario );
				
				$custo = new Custo();
				$jornal->setCusto( $custo );		
				
				$retorno = JornalModel::insert( $jornal );
				if( DataValidator::isEmpty($retorno['msg']) )
					echo json_encode( array('msg' => 'sucesso', 'jornal_id' => $retorno['jornal_id']) );
				
			} else
				echo json_encode( array('msg' => 'ja existe') );
			
		}
		catch(UserException $e){
			$msg = $e->getMessage();
		}
						
	}
		
?>