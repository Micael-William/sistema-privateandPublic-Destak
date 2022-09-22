<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("classes/Usuario.class.php");
require_once("classes/Perfil.class.php");

class UsuarioModel extends PersistModelAbstract
{

	public static function lista($status = null, $termo = null, $email = null, $perfil = 0)
	{
		$sql = " SELECT u.*, p.nome_perfil
					 FROM usuario u
					 INNER JOIN perfil p ON u.perfil_id=p.id 
					 ";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " u.status_usuario =:status ";
		}

		if (!DataValidator::isEmpty($termo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " u.nome_usuario LIKE _utf8 :termo COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($email)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " u.email_usuario LIKE :email ";
		}

		if (!DataValidator::isEmpty($perfil)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " u.perfil_id =:perfil ";
		}

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= " u.deleted=false ";

		$sql .= " ORDER BY u.nome_usuario ";

		$usuarios = array();
		$usuarioModel = new UsuarioModel();

		$query = $usuarioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($termo))
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($email))
			$query->bindValue(':email', "%$email%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($perfil))
			$query->bindValue(':perfil', $perfil, PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$usuario = new Usuario();
			$usuario->setId($linha->id);
			$usuario->setNome($linha->nome_usuario);
			$usuario->setEmail($linha->email_usuario);
			$usuario->setStatus($linha->status_usuario);

			$perfil = new Perfil();
			$perfil->setId($linha->perfil_id);
			$perfil->setNome($linha->nome_perfil);
			$usuario->setPerfil($perfil);

			$usuarios[] = $usuario;
		}

		return $usuarios;
	}

	public static function getById($usuario_id, $db = null)
	{
		$usuario = null;

		if (is_null($db)) {
			$usuarioModel = new UsuarioModel();
			$db = $usuarioModel->getDB();
		}

		if (DataValidator::isEmpty($usuario_id))
			throw new UserException('O Usuário deve ser identificado.');

		$sql = " SELECT u.*, p.nome_perfil
					FROM usuario u
					INNER JOIN perfil p ON u.perfil_id=p.id
					WHERE u.id=:usuario_id; ";

		$usuarioModel = new UsuarioModel();
		$query = $db->prepare($sql);
		$query->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$usuario = new Usuario();
		$usuario->setId($linha->id);
		$usuario->setStatus($linha->status_usuario);
		$usuario->setNome($linha->nome_usuario);
		$usuario->setEmail($linha->email_usuario);
		$usuario->setCpf($linha->cpf);
		$usuario->setDataEntrada($linha->data_entrada);
		$usuario->setSenha($linha->senha);

		$perfil = new Perfil();
		$perfil->setId($linha->perfil_id);
		$perfil->setNome($linha->nome_perfil);
		$usuario->setPerfil($perfil);

		return $usuario;
	}

	public static function insert($usuario)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($usuario->getPerfil()->getId()))
				throw new UserException('O campo Nível de Acesso é obrigatório.');

			if (DataValidator::isEmpty($usuario->getNome()))
				throw new UserException('O campo Nome é obrigatório.');

			if (DataValidator::isEmpty($usuario->getCpf()))
				throw new UserException('O campo CPF é obrigatório.');

			if (DataValidator::isEmpty($usuario->getEmail()))
				throw new UserException('O campo Email é obrigatório.');

			if (!filter_var($usuario->getEmail(), FILTER_VALIDATE_EMAIL))
				throw new UserException('O campo Email é inválido.');

			if (DataValidator::isEmpty($usuario->getSenha()))
				throw new UserException('O campo Senha é obrigatório.');

			$sql = " INSERT INTO usuario (nome_usuario, status_usuario, perfil_id, email_usuario, senha, cpf, data_entrada) VALUES (:nome_usuario, :status_usuario, :perfil_id, :email_usuario, :senha, :cpf, :data_entrada) ";

			$usuarioModel = new UsuarioModel();
			$query = $usuarioModel->getDB()->prepare($sql);
			$query->bindValue(':nome_usuario', $usuario->getNome(), PDO::PARAM_STR);
			$query->bindValue(':status_usuario', $usuario->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':perfil_id', $usuario->getPerfil()->getId(), PDO::PARAM_INT);
			$query->bindValue(':email_usuario', $usuario->getEmail(), PDO::PARAM_STR);
			$query->bindValue(':cpf', $usuario->getCpf(), PDO::PARAM_STR);
			$query->bindValue(':senha', md5($usuario->getSenha()), PDO::PARAM_STR);
			$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function update($usuario)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($usuario->getPerfil()->getId()))
				throw new UserException('O campo Nível de Acesso é obrigatório.');

			if (DataValidator::isEmpty($usuario->getNome()))
				throw new UserException('O campo Nome é obrigatório.');

			if (DataValidator::isEmpty($usuario->getEmail()))
				throw new UserException('O campo Email é obrigatório.');

			if (!filter_var($usuario->getEmail(), FILTER_VALIDATE_EMAIL))
				throw new UserException('O campo Email é inválido.');

			$sql = " UPDATE usuario SET 
						nome_usuario=:nome_usuario,
						status_usuario=:status_usuario,
						perfil_id=:perfil_id,
						email_usuario=:email_usuario,
						senha=:senha
						WHERE id=:usuario_id ";

			$usuarioModel = new UsuarioModel();
			$query = $usuarioModel->getDB()->prepare($sql);
			$query->bindValue(':nome_usuario', $usuario->getNome(), PDO::PARAM_STR);
			$query->bindValue(':status_usuario', $usuario->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':perfil_id', $usuario->getPerfil()->getId(), PDO::PARAM_INT);
			$query->bindValue(':email_usuario', $usuario->getEmail(), PDO::PARAM_STR);
			$query->bindValue(':usuario_id', $usuario->getId(), PDO::PARAM_STR);

			if (DataValidator::isEmpty($usuario->getSenha())) {
				$user = self::getById($usuario->getId());
				$psw = $user->getSenha();
			} else {
				$psw = md5($usuario->getSenha());
			}

			$query->bindValue(':senha', $psw, PDO::PARAM_STR);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function login($usuario)
	{

		if (DataValidator::isEmpty($usuario->getCpf()))
			throw new UserException('O campo CPF é obrigatório.');

		if (DataValidator::isEmpty($usuario->getSenha()))
			throw new UserException('O campo Senha é obrigatório.');

		$sql = " SELECT u.*, p.nome_perfil FROM usuario u
					 INNER JOIN perfil p ON u.perfil_id=p.id
					 WHERE u.cpf=:cpf 
					 	AND u.senha=:senha 
						AND u.status_usuario='A' 
						AND u.deleted=false; ";

		$usuarioModel = new UsuarioModel();
		$query = $usuarioModel->getDB()->prepare($sql);
		$query->bindValue(':cpf', $usuario->getCpf(), PDO::PARAM_STR);
		$query->bindValue(':senha', md5($usuario->getSenha()), PDO::PARAM_STR);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			throw new UserException('Usuário inválido.');

		$usuario = new Usuario();
		$usuario->setId($linha->id);;
		$usuario->setStatus($linha->status_usuario);
		$usuario->setNome($linha->nome_usuario);
		$usuario->setEmail($linha->email_usuario);
		$usuario->setSenha($linha->senha);

		$perfil = new Perfil();
		$perfil->setId($linha->perfil_id);
		$perfil->setNome($linha->nome_perfil);
		$perfil->setResponsabilidades(ResponsabilidadeModel::get_responsabilidades($perfil->getId()), $usuarioModel->getDB());
		$usuario->setPerfil($perfil);

		return $usuario;
	}

	public static function envioSenha($cpf)
	{

		if (DataValidator::isEmpty($cpf))
			throw new UserException('O campo CPF é obrigatório.');

		$sql = " SELECT id, nome_usuario, email_usuario FROM usuario
					 WHERE cpf=:cpf 
					 	AND status_usuario='A' 
						AND deleted=false; ";

		$usuarioModel = new UsuarioModel();
		$query = $usuarioModel->getDB()->prepare($sql);
		$query->bindValue(':cpf', $cpf, PDO::PARAM_STR);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			throw new UserException('CPF inexistente.');

		else {
			$usuario_id = $linha->id;
			$nome = $linha->nome_usuario;
			$email = $linha->email_usuario;
			$nova_senha = DataFilter::geraSenha(10);

			$mensagem = '
				Prezado usuário, 
				<br>
				<br>
				Conforme solicitação realizada, apresentamos nova senha de acesso:
				<br />' . $nova_senha;

			require_once("lib/Mail.php");
			$mail = Mail::Init();

			//$mail->Username   = 'sistema@sistemadestakpublicidade.com.br';

			// Define o remetente
			$mail->SetFrom('sistema@sistemadestakpublicidade.com.br', 'Destak Publicidade');

			// Define os destinatario(s)
			$mail->AddAddress($email, $nome);

			// Define a mensagem (Texto e Assunto)
			$mail->Subject  = "Destak Publicidade: Recuperação de Senha";
			$mail->Body = $mensagem;

			$enviado = $mail->Send();

			// Limpa os destinatarios e os anexos
			$mail->ClearAllRecipients();
			$mail->ClearAttachments();

			if ($enviado) {
				$sql = " UPDATE usuario SET senha=:senha, data_alteracao_senha=:data WHERE id=:usuario_id; ";

				$usuarioModel = new UsuarioModel();
				$query = $usuarioModel->getDB()->prepare($sql);
				$query->bindValue(':data', date("Y-m-d H:i:s"), PDO::PARAM_STR);
				$query->bindValue(':senha', md5($nova_senha), PDO::PARAM_STR);
				$query->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);

				$query->execute();
			}
		}
	}

	public static function delete($id)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($id))
				throw new UserException('O Usuário deve ser identificado.');

			$usuarioModel = new UsuarioModel();
			$query = $usuarioModel->getDB()->prepare("UPDATE usuario SET deleted = true WHERE ID = :ID;");
			$query->bindValue(':ID', $id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}
}
