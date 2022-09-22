<?php
	require_once("lib/DataValidator.php");
	require_once("models/SecretariaModel.php");

	$nome = $_REQUEST['nome_secretaria'];
	$estado = $_REQUEST['estado_lightbox'];
	$retorno = null;
	
	if( isset($nome) && 
		!DataValidator::isEmpty($nome) && 
		isset($estado) && 
		!DataValidator::isEmpty($estado)
	){
		
		try{
			$sec_cadastrada = SecretariaModel::getByNome($nome, $estado);	
			
			if( DataValidator::isEmpty($sec_cadastrada) ){	
				
				$secretaria = new Secretaria();
				$secretaria->setNome( isset($nome) && !DataValidator::isEmpty($nome) ? $nome : null );
				$secretaria->setEstado( isset($estado) && !DataValidator::isEmpty($estado) ? $estado : null );
				
				$retorno = SecretariaModel::insert( $secretaria );
                                
				if( DataValidator::isEmpty($retorno['msg']) )
					echo json_encode( array('msg' => 'sucesso', 'sec_id' => $retorno['secretaria_id']) );
			}  else
				echo json_encode( array('msg' => 'ja existe', 'sec_id' => $sec_cadastrada) );	
				
		}
		catch(UserException $e){
			$retorno['msg'] = $e->getMessage();
		}
						
	}
?>