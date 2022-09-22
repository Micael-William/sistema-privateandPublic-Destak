<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/DataValidator.php");
require_once("lib/UserException.php");
require_once("classes/Usuario.class.php");
require_once("classes/Pedido.class.php");
require_once("classes/PedidoEndereco.class.php");
require_once("classes/PedidoItem.class.php");
require_once("classes/PedidoFrete.class.php");
require_once("classes/PesquisaVendas.class.php");
require_once("classes/FaixaConsumo.class.php");
require_once("classes/FaixaFrequencia.class.php");
require_once("models/PedidoItemModel.php");
require_once("models/PedidoFreteModel.php");

class PedidoModel extends PersistModelAbstract
{

	/**
	 * Lista os pedidos da pessoa informada
	 * @param number $pessoaId
	 * @return multitype:Pedido
	 */
	public static function lista($pessoaId = 0)
	{
		$sql = "SELECT pd.*,
					tr.data_status, tr.status_transacao, tr.tipo_pagamento,
					tr.meio_pgto_codigo, tr.data_pgto, tr.vl_desconto, tr.vl_taxas, tr.data_credito, tr.num_parcelas, mp.desc_meio_pgto,
					co.qtde_animais, co.fequencia_compras_mes, co.faixa_consumo_ID, co.faixa_frequencia_ID,
					pe.nome, pe.tipo_pessoa, pe.cpf_cnpj, pe.tipo_relacionamento, pf.data_nascimento, pf.rg, us.email,
					en.logradouro, en.numero, en.complemento, en.bairro, en.cidade, en.estado, en.cep
				FROM pedido pd
					INNER JOIN transacao tr ON tr.pedido_ID = pd.ID
					LEFT JOIN meio_pgto mp ON mp.codigo = tr.meio_pgto_codigo
					INNER JOIN consumidor co ON co.pessoa_ID = pd.consumidor_pessoa_ID
					INNER JOIN pessoa pe ON pe.ID = co.pessoa_ID
					INNER JOIN usuario us ON us.pessoa_ID = pe.ID
					INNER JOIN pessoa_fisica pf ON pf.pessoa_ID = pe.ID
					INNER JOIN pedido_endereco en ON en.pedido_ID = pd.ID ";

		$where = false;

		if (!DataValidator::isEmpty($pessoaId)) {
			if (!$where) {
				$where = true;
				$sql .= " WHERE ";
			} else {
				$sql .= " AND ";
			}
			$sql .= " pd.consumidor_pessoa_ID =:pessoaId ";
		}

		$sql .= " ORDER BY pd.data_pedido DESC ";

		$model = new PedidoModel();
		$query = $model->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($pessoaId)) {
			$query->bindValue(':pessoaId', $pessoaId, PDO::PARAM_INT);
		}

		$query->execute();
		$pedidos = array();

		while ($linha = $query->fetchObject())
			$pedidos[] = self::montaPedido($linha);

		return $pedidos;
	}

	/**
	 * Retorna o Pedido do identificador solicitado.
	 * @param int $id
	 * @return Pedido
	 */
	public static function getById($id)
	{
		$model = new PedidoModel();

		$query = $model->getDB()->prepare(
			"SELECT pd.*,
					tr.data_status, tr.status_transacao, tr.tipo_pagamento,
					tr.meio_pgto_codigo, tr.data_pgto, tr.vl_desconto, tr.vl_taxas, tr.data_credito, tr.num_parcelas, mp.desc_meio_pgto,
					co.qtde_animais, co.fequencia_compras_mes, co.faixa_consumo_ID, co.faixa_frequencia_ID,
					pe.nome, pe.tipo_pessoa, pe.cpf_cnpj, pe.tipo_relacionamento, pf.data_nascimento, pf.rg, us.email,
					en.logradouro, en.numero, en.complemento, en.bairro, en.cidade, en.estado, en.cep
				FROM pedido pd
					LEFT JOIN transacao tr ON tr.pedido_ID = pd.ID
					LEFT JOIN meio_pgto mp ON mp.codigo = tr.meio_pgto_codigo
					INNER JOIN consumidor co ON co.pessoa_ID = pd.consumidor_pessoa_ID
					INNER JOIN pessoa pe ON pe.ID = co.pessoa_ID
					INNER JOIN usuario us ON us.pessoa_ID = pe.ID
					INNER JOIN pessoa_fisica pf ON pf.pessoa_ID = pe.ID
					INNER JOIN pedido_endereco en ON en.pedido_ID = pd.ID	
				WHERE pd.ID = :id;"

		);

		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();

		$pedido = null;
		$linha = $query->fetchObject();

		if ($linha) {
			$pedido = self::montaPedido($linha);
			$pedido->setItens(PedidoItemModel::lista($pedido));
			$pedido->setFretes(PedidoFreteModel::lista($pedido));
		}

		return $pedido;
	}

	private static function montaPedido($linha)
	{
		$pedido = new Pedido();
		$pedido->setId($linha->ID);

		$pedido->setConsumidor(new Consumidor());
		$pedido->getConsumidor()->setUsuario(new Usuario());
		$pedido->getConsumidor()->getUsuario()->setEmail($linha->email);
		$pedido->getConsumidor()->setFaixaConsumo(new FaixaConsumo());
		$pedido->getConsumidor()->getFaixaConsumo()->setId($linha->faixa_consumo_ID);
		$pedido->getConsumidor()->setFaixaFrequencia(new FaixaFrequencia());
		$pedido->getConsumidor()->getFaixaFrequencia()->setId($linha->faixa_frequencia_ID);
		$pedido->getConsumidor()->setQtdeAnimais($linha->qtde_animais);
		$pedido->getConsumidor()->setId($linha->consumidor_pessoa_ID);
		$pedido->getConsumidor()->setNome($linha->nome);
		$pedido->getConsumidor()->setCpfCnpj($linha->cpf_cnpj);
		$pedido->getConsumidor()->setRg($linha->rg);

		$pedido->setEndereco(new PedidoEndereco());
		$pedido->getEndereco()->setPedido($pedido);
		$pedido->getEndereco()->setLogradouro($linha->logradouro);
		$pedido->getEndereco()->setNumero($linha->numero);
		$pedido->getEndereco()->setComplemento($linha->complemento);
		$pedido->getEndereco()->setBairro($linha->bairro);
		$pedido->getEndereco()->setCidade($linha->cidade);
		$pedido->getEndereco()->setEstado($linha->estado);
		$pedido->getEndereco()->setCep($linha->cep);

		$pedido->setDataPedido(date_create($linha->data_pedido, new DateTimeZone('Etc/GMT+3')));
		$pedido->setCode($linha->code);

		if (!DataValidator::isEmpty($linha->data_status)) {
			$pedido->setDataStatus(date_create($linha->data_status, new DateTimeZone('Etc/GMT+3')));
			$pedido->setStatus($linha->status_transacao);

			$pedido->setTipoPagamento($linha->tipo_pagamento);

			if (!DataValidator::isEmpty($linha->meio_pgto_codigo)) {
				$pedido->setMeioPgto(new MeioPgto());
				$pedido->getMeioPgto()->setCodigo($linha->meio_pgto_codigo);
				$pedido->getMeioPgto()->setDescricao($linha->desc_meio_pgto);
			}

			if (!DataValidator::isEmpty($linha->data_pgto))
				$pedido->setDataPgto(date_create($linha->data_pgto, new DateTimeZone('Etc/GMT+3')));

			$pedido->setValorDesconto($linha->vl_desconto);
			$pedido->setValorTaxas($linha->vl_taxas);

			if (!DataValidator::isEmpty($linha->data_credito))
				$pedido->setDataCredito(date_create($linha->data_credito, new DateTimeZone('Etc/GMT+3')));

			$pedido->setNumParcelas($linha->num_parcelas);
		}

		return $pedido;
	}

	/**
	 * INSERT na tabela 'pedido'
	 * @param Pedido $pedido
	 * @param PDO $db
	 * @throws Exception
	 */
	public static function insert($pedido, $db)
	{
		if (DataValidator::isEmpty($pedido) || !$pedido instanceof Pedido)
			throw UserEception("Parâmetro 'pedido' é inválido");

		if (DataValidator::isEmpty($db)) {
			$model = new PedidoModel();
			$db = $model->getDB();
		}

		$query = $db->prepare(
			"INSERT INTO pedido (consumidor_pessoa_ID, data_pedido, code)
				VALUES (:consumidor_pessoa_ID, :data_pedido, :code);"
		);
		$query->bindValue(':consumidor_pessoa_ID', $pedido->getConsumidor()->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_pedido', date_format($pedido->getDataPedido(), "Y-m-d H:i:s"), PDO::PARAM_STR);

		if (DataValidator::isEmpty($pedido->getCode()))
			$query->bindValue(':code', null, PDO::PARAM_NULL);
		else
			$query->bindValue(':code', $pedido->getCode(), PDO::PARAM_STR);

		$query->execute();

		$query = $db->prepare("SELECT last_insert_id() AS ID;");
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			throw new Exception('Problemas na inclusão do pedido');

		$pedido->setId($linha->ID);

		$query = $db->prepare(
			"INSERT INTO pedido_endereco (pedido_ID, logradouro, numero, complemento, bairro, cidade, estado, cep)
				VALUES (:pedido_ID, :logradouro, :numero, :complemento, :bairro, :cidade, :estado, :cep);"
		);
		$query->bindValue(':pedido_ID', $pedido->getId(), PDO::PARAM_INT);
		$query->bindValue(':logradouro', $pedido->getEndereco()->getLogradouro(), PDO::PARAM_STR);
		$query->bindValue(':numero', $pedido->getEndereco()->getNumero(), PDO::PARAM_INT);
		$query->bindValue(':complemento', $pedido->getEndereco()->getComplemento(), PDO::PARAM_STR);
		$query->bindValue(':bairro', $pedido->getEndereco()->getBairro(), PDO::PARAM_STR);
		$query->bindValue(':cidade', $pedido->getEndereco()->getCidade(), PDO::PARAM_STR);
		$query->bindValue(':estado', $pedido->getEndereco()->getEstado(), PDO::PARAM_STR);
		$query->bindValue(':cep', DataFilter::numeric($pedido->getEndereco()->getCep()), PDO::PARAM_STR);
		$query->execute();
	}

	/**
	 * UPDATE na tabela 'pedido'
	 * @param Pedido $pedido
	 * @param PDO $db
	 * @throws Exception
	 */
	public static function update($pedido, $db)
	{
		if (DataValidator::isEmpty($pedido) || !$pedido instanceof Pedido)
			throw UserEception("Parâmetro 'pedido' é inválido");

		if (DataValidator::isEmpty($pedido->getId()))
			throw UserEception("Pedido deve ser identificado");

		if (DataValidator::isEmpty($db)) {
			$model = new PedidoModel();
			$db = $model->getDB();
		}

		$query = $db->prepare(
			"UPDATE pedido SET
					consumidor_pessoa_ID = :consumidor_pessoa_ID,
					data_pedido = :data_pedido,
					code = :code
				WHERE ID = :ID;"
		);
		$query->bindValue(':ID', $pedido->getId(), PDO::PARAM_INT);
		$query->bindValue(':consumidor_pessoa_ID', $pedido->getConsumidor()->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_pedido', date_format($pedido->getDataPedido(), "Y-m-d H:i:s"), PDO::PARAM_STR);

		if (DataValidator::isEmpty($pedido->getCode()))
			$query->bindValue(':code', null, PDO::PARAM_NULL);
		else
			$query->bindValue(':code', $pedido->getCode(), PDO::PARAM_STR);

		$query->execute();

		$query = $db->prepare(
			"UPDATE pedido_endereco SET
					logradouro = :logradouro,
					numero = :numero,
					complemento = :complemento,
					bairro = :bairro,
					cidade = :cidade,
					estado = :estado,
					cep = :cep
				WHERE pedido_ID = :pedido_ID;"
		);
		$query->bindValue(':pedido_ID', $pedido->getId(), PDO::PARAM_INT);
		$query->bindValue(':logradouro', $pedido->getEndereco()->getLogradouro(), PDO::PARAM_STR);
		$query->bindValue(':numero', $pedido->getEndereco()->getNumero(), PDO::PARAM_INT);
		$query->bindValue(':complemento', $pedido->getEndereco()->getComplemento(), PDO::PARAM_STR);
		$query->bindValue(':bairro', $pedido->getEndereco()->getBairro(), PDO::PARAM_STR);
		$query->bindValue(':cidade', $pedido->getEndereco()->getCidade(), PDO::PARAM_STR);
		$query->bindValue(':estado', $pedido->getEndereco()->getEstado(), PDO::PARAM_STR);
		$query->bindValue(':cep', DataFilter::numeric($pedido->getEndereco()->getCep()), PDO::PARAM_STR);
		$query->execute();
	}

	/**
	 * DELETE na tabela 'pedido'
	 * @param int $id
	 * @param PDO $db
	 * @throws Exception
	 */
	public static function delete($id, $db)
	{
		if (DataValidator::isEmpty($id))
			throw UserEception("Pedido deve ser identificado");

		if (DataValidator::isEmpty($db)) {
			$model = new PedidoModel();
			$db = $model->getDB();
		}

		$query = $db->prepare(
			"DELETE FROM pedido WHERE ID = :ID;"
		);
		$query->bindValue(':ID', $id, PDO::PARAM_INT);
		$query->execute();
	}

	/**
	 * Checa a consistência do objeto 'Pedido'
	 * @param Pedido $pedido
	 * @return multitype:string |multitype:
	 */
	private static function consiste($pedido)
	{
		if (DataValidator::isEmpty($pedido))
			return array("Pedido deve ser informado.");

		$msg = array();

		if (DataValidator::isEmpty($pedido->getConsumidor()) || DataValidator::isEmpty($pedido->getConsumidor()->getId()))
			$msg[] = "Consumidor deve ser identificado.";

		if (DataValidator::isEmpty($pedido->getDataPedido()))
			$msg[] = "Data do pedido deve ser informada.";

		if (DataValidator::isEmpty($pedido->getEndereco()))
			$msg[] = "Endereço deve ser identificado.";

		if (DataValidator::isEmpty($pedido->getEndereco()->getLogradouro()))
			$msg[] = 'O campo Endereço é obrigatório.';

		if (DataValidator::isEmpty($pedido->getEndereco()->getNumero()))
			$msg[] = 'O campo Número é obrigatório.';

		if (DataValidator::isEmpty($pedido->getEndereco()->getBairro()))
			$msg[] = 'O campo Bairro é obrigatório.';

		if (DataValidator::isEmpty($pedido->getEndereco()->getCep()))
			$msg[] = 'O campo Cep é obrigatório.';

		if (DataValidator::isEmpty($pedido->getEndereco()->getCidade()))
			$msg[] = 'O campo Cidade é obrigatório.';

		if (DataValidator::isEmpty($pedido->getEndereco()->getEstado()))
			$msg[] = 'O campo Estado é obrigatório.';

		return $msg;
	}

	/**
	 * Inclui um novo pedido.
	 * @param Pedido $pedido
	 * @throws Exception
	 * @return array
	 */
	public static function novo($pedido)
	{
		$msg = self::consiste($pedido);

		if (DataValidator::isEmpty($msg)) {
			$model = new PedidoModel();
			$model->getDB()->beginTransaction();

			try {
				self::insert($pedido, $model->getDB());

				if (!DataValidator::isEmpty($pedido->getItens()))
					foreach ($pedido->getItens() as $item)
						PedidoItemModel::insert($item, $model->getDB());

				if (!DataValidator::isEmpty($pedido->getFretes()))
					foreach ($pedido->getFretes() as $frete)
						PedidoFreteModel::insert($frete, $model->getDB());
			} catch (Exception $e) {
				$model->getDB()->rollback();
				throw $e;
			}

			$model->getDB()->commit();
		}

		return $msg;
	}

	/**
	 * Altera o pedido informado.
	 * @param Pedido $pedido
	 * @throws Exception
	 * @return array
	 */
	public static function altera($pedido)
	{
		$msg = self::consiste($pedido);

		if (DataValidator::isEmpty($msg) && DataValidator::isEmpty($pedido->getId()))
			$msg[] = "Pedido deve ser identificado";

		if (DataValidator::isEmpty($msg)) {
			$model = new PedidoModel();
			$model->getDB()->beginTransaction();

			try {
				self::update($pedido, $model->getDB());

				PedidoItemModel::limpa($pedido->getId(), null, $model->getDB());

				if (!DataValidator::isEmpty($pedido->getItens()))
					foreach ($pedido->getItens() as $item)
						PedidoItemModel::insert($item, $model->getDB());

				PedidoFreteModel::limpa($pedido->getId(), null, $model->getDB());

				if (!DataValidator::isEmpty($pedido->getFretes()))
					foreach ($pedido->getFretes() as $frete)
						PedidoFreteModel::insert($frete, $model->getDB());
			} catch (UserException $e) {
				$model->getDB()->rollback();
				$msg[] = $e->getMessage();
			} catch (Exception $e) {
				$model->getDB()->rollback();
				throw $e;
			}

			$model->getDB()->commit();
		}

		return $msg;
	}

	/**
	 * Atualiza o status do pedido com base em uma transação do PagSeguro
	 * @param Pedido $pedido
	 * @throws Exception
	 */
	public static function transacao($pedido, $inclui, $statusAnterior)
	{
		if (DataValidator::isEmpty($pedido) || !$pedido instanceof Pedido)
			throw UserEception("Parâmetro 'pedido' é inválido");

		if (DataValidator::isEmpty($pedido->getId()))
			throw UserEception("Pedido deve ser identificado");

		$model = new PedidoModel();
		$model->getDB()->beginTransaction();

		try {
			if ($inclui) {
				$sql = "INSERT INTO transacao (pedido_ID, data_status, status_transacao, tipo_pagamento, meio_pgto_codigo, data_pgto, vl_desconto, vl_taxas, data_credito, num_parcelas)
						VALUES (:pedido_ID, :data_status, :status_transacao, :tipo_pagamento, :meio_pgto_codigo, :data_pgto, :vl_desconto, :vl_taxas, :data_credito, :num_parcelas);";
			} else {
				$sql = "UPDATE transacao SET
							data_status = :data_status,
							status_transacao = :status_transacao,
							tipo_pagamento = :tipo_pagamento,
							meio_pgto_codigo = :meio_pgto_codigo,
							data_pgto = :data_pgto,
							vl_desconto = :vl_desconto,
							vl_taxas = :vl_taxas,
							data_credito = :data_credito,
							num_parcelas = :num_parcelas
						WHERE pedido_ID = :pedido_ID;";
			}

			$query = $model->getDB()->prepare($sql);
			$query->bindValue(':pedido_ID', $pedido->getId(), PDO::PARAM_INT);
			$query->bindValue(':data_status', date_format($pedido->getDataStatus(), "Y-m-d H:i:s"), PDO::PARAM_STR);
			$query->bindValue(':status_transacao', $pedido->getStatus(), PDO::PARAM_STR);

			$query->bindValue(':tipo_pagamento', $pedido->getTipoPagamento(), PDO::PARAM_STR);
			$query->bindValue(':meio_pgto_codigo', $pedido->getMeioPgto()->getCodigo(), PDO::PARAM_STR);

			if (DataValidator::isEmpty($pedido->getDataPgto()))
				$query->bindValue(':data_pgto', null, PDO::PARAM_NULL);
			else
				$query->bindValue(':data_pgto', date_format($pedido->getDataPgto(), "Y-m-d H:i:s"), PDO::PARAM_STR);

			$query->bindValue(':vl_desconto', $pedido->getValorDesconto());
			$query->bindValue(':vl_taxas', $pedido->getValorTaxas());

			if (DataValidator::isEmpty($pedido->getDataCredito()))
				$query->bindValue(':data_credito', null, PDO::PARAM_NULL);
			else
				$query->bindValue(':data_credito', date_format($pedido->getDataCredito(), "Y-m-d H:i:s"), PDO::PARAM_STR);

			$query->bindValue(':num_parcelas', $pedido->getNumParcelas(), PDO::PARAM_INT);

			$query->execute();

			// Ocorreu mudança de status do pedido
			if ($statusAnterior != $pedido->getStatus()) {
				// Calcula o rateio das taxas do PagSeguro e o percentual de comissão
				if ($pedido->getStatus() == '1') // Aguardando pagamento
				{
					foreach ($pedido->getFretes() as $frete) {
						$totalProdutoLoja = 0;

						foreach ($pedido->getItens() as $item)
							if ($item->getLojista()->getPessoa()->getId() == $frete->getLojista()->getPessoa()->getId())
								$totalProdutoLoja += $item->getQtde() * $item->getValorUnitario();

						$frete->setValorTaxas(round(($totalProdutoLoja / $pedido->getTotalPedido()) * $pedido->getValorTaxas(), 2));
						$frete->setValorComissao(round($totalProdutoLoja * self::getFatorComissao($frete->getLojista()->getPessoa()->getId(), $model->getDB()), 2));

						PedidoFreteModel::update($frete, $model->getDB());
					}
				}

				// Atualiza o estoque dos produtos do lojista
				else if ($pedido->getStatus() == '3') // Paga
				{
					foreach ($pedido->getItens() as $item) {
						$estoque = EstoqueModel::getById($item->getProduto()->getId(), $item->getLojista()->getPessoa()->getId(), $model->getDB());
						$estoque->setQtde($estoque->getQtde() - $item->getQtde());

						EstoqueModel::update($estoque, null, $model->getDB());
					}
				}

				// Reverte a baixa do estoque do lojista em caso de cancelamento ou devolução
				else if ($pedido->getStatus() == '6' || $pedido->getStatus() == '7') // Devolvida ou Cancelada
				{
					foreach ($pedido->getItens() as $item) {
						$estoque = EstoqueModel::getById($item->getProduto()->getId(), $item->getLojista()->getPessoa()->getId(), $model->getDB());
						$estoque->setQtde($estoque->getQtde() + $item->getQtde());

						EstoqueModel::update($estoque, null, $model->getDB());
					}
				}
			}
		} catch (Exception $e) {
			$model->getDB()->rollback();
			throw $e;
		}

		$model->getDB()->commit();
	}

	/**
	 * Lista os pedidos a partir da pesquisa informada
	 * @param PesquisaVendas $pesquisa
	 * @return array
	 */
	public static function vendasPedido($pesquisa, $pagina = 0, $qtd_pagina = 0)
	{
		if (DataValidator::isEmpty($pesquisa) || !$pesquisa instanceof PesquisaVendas || $pesquisa->getTipoBusca() != 'pedido')
			throw new Exception("Objeto de pesquisa informado é inválido");

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					pd.ID,
					MIN(pd.data_pedido) AS data_pedido,
					MIN(pe1.nome) AS nome_consumidor,
					MIN(tr.status_transacao) AS status_transacao,
					ROUND( SUM(pi.qtde * pi.vl_unitario), 2) AS total_pedido,
					SUM(pf.vl_comissao) AS vl_comissao,
					IF( MIN(pf.entregue) = MAX(pf.entregue), MIN(pf.entregue), 2) AS entregue
				FROM pedido pd
					INNER JOIN pedido_item pi ON pi.pedido_ID = pd.ID
					INNER JOIN pedido_frete pf ON pf.pedido_ID = pd.ID AND pf.lojista_pessoa_ID = pi.estoque_lojista_pessoa_ID
					INNER JOIN produto pr ON pr.ID = pi.estoque_produto_ID
					INNER JOIN transacao tr ON tr.pedido_ID = pd.ID
					INNER JOIN pessoa pe1 ON pe1.ID = pd.consumidor_pessoa_ID ";

		/*
					ROUND( IF( MIN(tr.status_transacao) = '1',
								SUM(pi.qtde * pi.vl_unitario) * (MIN(IFNULL(IFNULL(co1.perc_comissao, co2.perc_comissao), 0)) / 100),
								0), 2) AS vl_comissao,

					LEFT JOIN condicao co1 ON co1.lojista_pessoa_ID = pi.estoque_lojista_pessoa_ID
					LEFT JOIN condicao co2 ON co2.lojista_pessoa_ID IS NULL ";
					*/
		$where = false;

		if (!DataValidator::isEmpty($pesquisa->getPedido())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.ID = :ID ";
		}

		if (!DataValidator::isEmpty($pesquisa->getLojista())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "EXISTS(
							SELECT * FROM pedido_frete fr, pessoa lo
							WHERE fr.pedido_ID = pd.ID 
							AND lo.ID = fr.lojista_pessoa_ID ";

			$sql .= is_numeric($pesquisa->getLojista()) ? "AND fr.lojista_pessoa_ID = :lojista) " : "AND lo.nome LIKE _utf8 :lojista COLLATE utf8_unicode_ci) ";
		}

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.data_pedido BETWEEN CONCAT(:dataDe, ' 00:00:00') AND CONCAT(:dataAte, ' 23:59:59') ";
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()) || !DataValidator::isEmpty($pesquisa->getCategoriaId()) || !DataValidator::isEmpty($pesquisa->getSubcategoriaId())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "EXISTS(
							SELECT * FROM produto_categoria pc
							WHERE pc.produto_ID = pi.estoque_produto_ID " . (!DataValidator::isEmpty($pesquisa->getModuloId()) ? "AND pc.modulo_ID = :moduloId " : "") . (!DataValidator::isEmpty($pesquisa->getCategoriaId()) ? "AND pc.categoria_ID = :categoriaId " : "") . (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()) ? "AND pc.subcategoria_ID = :subcategoriaId " : "") .
				") ";
		}

		if (!DataValidator::isEmpty($pesquisa->getProduto())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getProduto()) ? "pr.cod_produto LIKE :produto " : "pr.nome_produto LIKE _utf8 :produto COLLATE utf8_unicode_ci ";
		}

		$sql .= " GROUP BY pd.ID ORDER BY pd.ID DESC ";

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$model = new PedidoModel();
		$query = $model->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($pesquisa->getPedido()))
			$query->bindValue(':ID', $pesquisa->getPedido(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getLojista()))
			if (is_numeric($pesquisa->getLojista()))
				$query->bindValue(':lojista', $pesquisa->getLojista(), PDO::PARAM_INT);
			else
				$query->bindValue(':lojista', "%" . $pesquisa->getLojista() . "%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			$query->bindValue(':dataDe', substr($pesquisa->getDataDe(), 6, 4) . "-" . substr($pesquisa->getDataDe(), 3, 2) . "-" . substr($pesquisa->getDataDe(), 0, 2), PDO::PARAM_STR);
			$query->bindValue(':dataAte', substr($pesquisa->getDataAte(), 6, 4) . "-" . substr($pesquisa->getDataAte(), 3, 2) . "-" . substr($pesquisa->getDataAte(), 0, 2), PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()))
			$query->bindValue(':moduloId', $pesquisa->getModuloId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getCategoriaId()))
			$query->bindValue(':categoriaId', $pesquisa->getCategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()))
			$query->bindValue(':subcategoriaId', $pesquisa->getSubcategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getProduto()))
			if (is_numeric($pesquisa->getProduto()))
				$query->bindValue(':produto', $pesquisa->getProduto() . "%", PDO::PARAM_STR);
			else
				$query->bindValue(':produto', "%" . $pesquisa->getProduto() . "%", PDO::PARAM_STR);

		$query->execute();
		$pedidos = array();

		while ($linha = $query->fetchObject())
			$pedidos[] = array(
				"pedido_ID" => $linha->ID,
				"data_pedido" => date_create($linha->data_pedido, new DateTimeZone('Etc/GMT+3')),
				"nome_consumidor" => $linha->nome_consumidor,
				"status_pedido" => $linha->status_transacao,
				"total_pedido" => $linha->total_pedido,
				"vl_comissao" => $linha->vl_comissao,
				"entregue" => $linha->entregue
			);

		$query = $model->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query->execute();

		$linha = $query->fetchObject();
		$totalLinhas = $linha ? $linha->frows : 0;

		return array("total" => $totalLinhas, "pedidos" => $pedidos);
	}

	/**
	 * Lista os pedidos a partir da pesquisa informada
	 * @param PesquisaVendas $pesquisa
	 * @return array
	 */
	public static function vendasLojista($pesquisa, $pagina = 0, $qtd_pagina = 0)
	{
		if (DataValidator::isEmpty($pesquisa) || !$pesquisa instanceof PesquisaVendas || $pesquisa->getTipoBusca() != 'lojista')
			throw new Exception("Objeto de pesquisa informado é inválido");

		if (DataValidator::isEmpty($pesquisa->getLojista()))
			throw new UserException("Lojista deve ser informado");

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					pd.ID,
					pi.estoque_produto_ID,
					pi.estoque_lojista_pessoa_ID,
					MIN(pd.data_pedido) AS data_pedido,
					MIN(pr.cod_produto) AS cod_produto,
					MIN(pr.nome_produto) AS nome_produto,
					MIN(pe1.nome) AS nome_consumidor,
					MIN(tr.status_transacao) AS status_transacao,
					ROUND( SUM(pi.qtde * pi.vl_unitario), 2) AS total_produto,
					MIN(pf.vl_comissao) AS vl_comissao,
					MIN(pf.entregue) AS entregue
				FROM pedido pd
					INNER JOIN pedido_item pi ON pi.pedido_ID = pd.ID
					INNER JOIN pedido_frete pf ON pf.pedido_ID = pd.ID AND pf.lojista_pessoa_ID = pi.estoque_lojista_pessoa_ID
					INNER JOIN produto pr ON pr.ID = pi.estoque_produto_ID
					INNER JOIN transacao tr ON tr.pedido_ID = pd.ID
					INNER JOIN meio_pgto mp ON mp.codigo = tr.meio_pgto_codigo
					INNER JOIN pessoa pe1 ON pe1.ID = pd.consumidor_pessoa_ID
					INNER JOIN pessoa pe2 ON pe2.ID = pi.estoque_lojista_pessoa_ID ";

		/*
					ROUND( IF( MIN(tr.status_transacao) = '1',
								SUM(pi.qtde * pi.vl_unitario) * (MIN(IFNULL(IFNULL(co1.perc_comissao, co2.perc_comissao), 0)) / 100),
								0), 2) AS vl_comissao,

					LEFT JOIN condicao co1 ON co1.lojista_pessoa_ID = pi.estoque_lojista_pessoa_ID
					LEFT JOIN condicao co2 ON co2.lojista_pessoa_ID IS NULL ";
					*/

		$where = false;

		if (!DataValidator::isEmpty($pesquisa->getPedido())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.ID = :ID ";
		}

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= is_numeric($pesquisa->getLojista()) ? "pi.estoque_lojista_pessoa_ID = :lojista " : "pe2.nome LIKE _utf8 :lojista COLLATE utf8_unicode_ci ";

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.data_pedido BETWEEN CONCAT(:dataDe, ' 00:00:00') AND CONCAT(:dataAte, ' 23:59:59') ";
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()) || !DataValidator::isEmpty($pesquisa->getCategoriaId()) || !DataValidator::isEmpty($pesquisa->getSubcategoriaId())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "EXISTS(
							SELECT * FROM produto_categoria pc
							WHERE pc.produto_ID = pi.estoque_produto_ID " . (!DataValidator::isEmpty($pesquisa->getModuloId()) ? "AND pc.modulo_ID = :moduloId " : "") . (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()) ? "AND pc.subcategoria_ID = :subcategoriaId " : "") . (!DataValidator::isEmpty($pesquisa->getCategoriaId()) ? "AND pc.categoria_ID = :categoriaId " : "") .
				") ";
		}

		if (!DataValidator::isEmpty($pesquisa->getProduto())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getProduto()) ? "pr.cod_produto LIKE :produto " : "pr.nome_produto LIKE _utf8 :produto COLLATE utf8_unicode_ci ";
		}

		//$sql .= "GROUP BY pd.ID, pi.estoque_produto_ID, pi.estoque_lojista_pessoa_ID ORDER BY pd.ID DESC ";
		$sql .= "GROUP BY pd.ID ORDER BY pd.ID DESC ";

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$model = new PedidoModel();
		$query = $model->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($pesquisa->getPedido()))
			$query->bindValue(':ID', $pesquisa->getPedido(), PDO::PARAM_INT);

		if (is_numeric($pesquisa->getLojista()))
			$query->bindValue(':lojista', $pesquisa->getLojista(), PDO::PARAM_INT);
		else
			$query->bindValue(':lojista', "%" . $pesquisa->getLojista() . "%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			$query->bindValue(':dataDe', substr($pesquisa->getDataDe(), 6, 4) . "-" . substr($pesquisa->getDataDe(), 3, 2) . "-" . substr($pesquisa->getDataDe(), 0, 2), PDO::PARAM_STR);
			$query->bindValue(':dataAte', substr($pesquisa->getDataAte(), 6, 4) . "-" . substr($pesquisa->getDataAte(), 3, 2) . "-" . substr($pesquisa->getDataAte(), 0, 2), PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()))
			$query->bindValue(':moduloId', $pesquisa->getModuloId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getCategoriaId()))
			$query->bindValue(':categoriaId', $pesquisa->getCategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()))
			$query->bindValue(':subcategoriaId', $pesquisa->getSubcategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getProduto()))
			if (is_numeric($pesquisa->getProduto()))
				$query->bindValue(':produto', $pesquisa->getProduto() . "%", PDO::PARAM_STR);
			else
				$query->bindValue(':produto', "%" . $pesquisa->getProduto() . "%", PDO::PARAM_STR);

		$query->execute();
		$pedidos = array();

		while ($linha = $query->fetchObject())
			$pedidos[] = array(
				"pedido_ID" => $linha->ID,
				"produto_ID" => $linha->estoque_produto_ID,
				"cod_produto" => $linha->cod_produto,
				"nome_produto" => $linha->nome_produto,
				"lojista_ID" => $linha->estoque_lojista_pessoa_ID,
				"data_pedido" => date_create($linha->data_pedido, new DateTimeZone('Etc/GMT+3')),
				"nome_consumidor" => $linha->nome_consumidor,
				"status_pedido" => $linha->status_transacao,
				"total_produto" => $linha->total_produto,
				"vl_comissao" => $linha->vl_comissao,
				"entregue" => $linha->entregue
			);

		$query = $model->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query->execute();

		$linha = $query->fetchObject();
		$totalLinhas = $linha ? $linha->frows : 0;

		return array("total" => $totalLinhas, "pedidos" => $pedidos);
	}

	/**
	 * Retorna o fator relativo ao % de comissão do lojista informado
	 * @param int $lojistaId
	 * @return number
	 */
	public static function getFatorComissao($lojistaId, $db)
	{
		if (DataValidator::isEmpty($db)) {
			$model = new PedidoModel();
			$db = $model->getDB();
		}

		$query = $db->prepare(
			"SELECT IFNULL(IFNULL(co1.perc_comissao, co2.perc_comissao), 0) / 100 AS perc_comissao
				FROM lojista lo
					LEFT JOIN condicao co1 ON co1.lojista_pessoa_ID = lo.pessoa_ID
					LEFT JOIN condicao co2 ON co2.lojista_pessoa_ID IS NULL
				WHERE lo.pessoa_ID = :pessoa_ID;"
		);
		$query->bindValue(':pessoa_ID', $lojistaId, PDO::PARAM_INT);
		$query->execute();

		$pedido = null;
		$linha = $query->fetchObject();

		return floatval($linha ? $linha->perc_comissao : 0);
	}

	/**
	 * Lista os pedidos a partir da pesquisa informada
	 * @param PesquisaVendas $pesquisa
	 * @return array
	 */
	public static function financeiro($pesquisa, $pagina = 0, $qtd_pagina = 0)
	{
		if (DataValidator::isEmpty($pesquisa) || !$pesquisa instanceof PesquisaVendas)
			throw new Exception("Objeto de pesquisa informado é inválido");

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					pf.pedido_ID,
					pf.lojista_pessoa_id,
					MIN(pd.data_pedido) AS data_pedido,
					MIN(pe1.nome) AS nome_consumidor,
					MIN(pe2.nome_fantasia) AS nome_lojista,
					MIN(tr.status_transacao) AS status_transacao,
					ROUND( SUM(pi.qtde * pi.vl_unitario), 2) AS total_produtos,
					MIN( pf.preco_frete ) AS preco_frete,
					MIN( pf.vl_taxas ) AS vl_taxas,
					MIN( pf.vl_comissao ) AS vl_comissao
				FROM pedido pd
					INNER JOIN pedido_item pi ON pi.pedido_ID = pd.ID
					INNER JOIN pedido_frete pf ON pf.pedido_ID = pd.ID AND pf.lojista_pessoa_ID = pi.estoque_lojista_pessoa_ID
					INNER JOIN produto pr ON pr.ID = pi.estoque_produto_ID
					INNER JOIN transacao tr ON tr.pedido_ID = pd.ID
					INNER JOIN pessoa pe1 ON pe1.ID = pd.consumidor_pessoa_ID
					INNER JOIN pessoa_juridica pe2 ON pe2.pessoa_ID = pf.lojista_pessoa_id ";

		$where = false;

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= "tr.status_transacao = '4' ";

		if (!DataValidator::isEmpty($pesquisa->getPedido())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.ID = :ID ";
		}

		if (!DataValidator::isEmpty($pesquisa->getLojista())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getLojista()) ? "pi.estoque_lojista_pessoa_ID = :lojista " : "pe2.nome_fantasia LIKE _utf8 :lojista COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.data_pedido BETWEEN CONCAT(:dataDe, ' 00:00:00') AND CONCAT(:dataAte, ' 23:59:59') ";
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()) || !DataValidator::isEmpty($pesquisa->getCategoriaId()) || !DataValidator::isEmpty($pesquisa->getSubcategoriaId())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "EXISTS(
							SELECT * FROM produto_categoria pc
							WHERE pc.produto_ID = pi.estoque_produto_ID " . (!DataValidator::isEmpty($pesquisa->getModuloId()) ? "AND pc.modulo_ID = :moduloId " : "") . (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()) ? "AND pc.subcategoria_ID = :subcategoriaId " : "") . (!DataValidator::isEmpty($pesquisa->getCategoriaId()) ? "AND pc.categoria_ID = :categoriaId " : "") .
				") ";
		}

		if (!DataValidator::isEmpty($pesquisa->getProduto())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getProduto()) ? "pr.cod_produto LIKE :produto) " : "pr.nome_produto LIKE _utf8 :produto COLLATE utf8_unicode_ci) ";
		}

		$sql .= " GROUP BY pf.pedido_ID, pf.lojista_pessoa_id ORDER BY pf.pedido_ID DESC ";

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$model = new PedidoModel();
		$query = $model->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($pesquisa->getPedido()))
			$query->bindValue(':ID', $pesquisa->getPedido(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getLojista()))
			if (is_numeric($pesquisa->getLojista()))
				$query->bindValue(':lojista', $pesquisa->getLojista(), PDO::PARAM_INT);
			else
				$query->bindValue(':lojista', "%" . $pesquisa->getLojista() . "%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			$query->bindValue(':dataDe', substr($pesquisa->getDataDe(), 6, 4) . "-" . substr($pesquisa->getDataDe(), 3, 2) . "-" . substr($pesquisa->getDataDe(), 0, 2), PDO::PARAM_STR);
			$query->bindValue(':dataAte', substr($pesquisa->getDataAte(), 6, 4) . "-" . substr($pesquisa->getDataAte(), 3, 2) . "-" . substr($pesquisa->getDataAte(), 0, 2), PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()))
			$query->bindValue(':moduloId', $pesquisa->getModuloId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getCategoriaId()))
			$query->bindValue(':categoriaId', $pesquisa->getCategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()))
			$query->bindValue(':subcategoriaId', $pesquisa->getSubcategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getProduto()))
			if (is_numeric($pesquisa->getProduto()))
				$query->bindValue(':produto', $pesquisa->getProduto() . "%", PDO::PARAM_STR);
			else
				$query->bindValue(':produto', "%" . $pesquisa->getProduto() . "%", PDO::PARAM_STR);

		$query->execute();
		$pedidos = array();

		while ($linha = $query->fetchObject())
			$pedidos[] = array(
				"pedido_ID" => $linha->pedido_ID,
				"lojista_ID" => $linha->lojista_pessoa_id,
				"data_pedido" => date_create($linha->data_pedido, new DateTimeZone('Etc/GMT+3')),
				"nome_consumidor" => $linha->nome_consumidor,
				"nome_lojista" => $linha->nome_lojista,
				"status_pedido" => $linha->status_transacao,
				"total_produtos" => $linha->total_produtos,
				"preco_frete" => $linha->preco_frete,
				"vl_taxas" => $linha->vl_taxas,
				"vl_comissao" => $linha->vl_comissao
			);

		$query = $model->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query->execute();

		$linha = $query->fetchObject();
		$totalLinhas = $linha ? $linha->frows : 0;

		return array("total" => $totalLinhas, "pedidos" => $pedidos);
	}

	/**
	 * Lista os pedidos a partir da pesquisa informada para geração de planilha Excel
	 * @param PesquisaVendas $pesquisa
	 * @return array
	 */
	public static function financeiroExcel($pesquisa)
	{
		if (DataValidator::isEmpty($pesquisa) || !$pesquisa instanceof PesquisaVendas)
			throw new Exception("Objeto de pesquisa informado é inválido");

		$sql = "SELECT
					pi.*,
					pr.nome_produto,
					fa.nome_fabricante,
					ma.nome_marca,
					pd.data_pedido,
					tr.status_transacao,
					pe1.nome AS nome_consumidor,
					en1.cep AS cep_consumidor,
					en1.bairro AS bairro_consumidor,
					pe2.nome_fantasia AS nome_lojista,
					en2.cep AS cep_lojista,
					en2.bairro AS bairro_lojista
				FROM pedido pd
					INNER JOIN pedido_item pi ON pi.pedido_ID = pd.ID
					INNER JOIN produto pr ON pr.ID = pi.estoque_produto_ID
					LEFT JOIN fabricante fa ON fa.ID = pr.fabricante_ID
					LEFT JOIN marca ma ON fa.ID = pr.marca_ID
					INNER JOIN transacao tr ON tr.pedido_ID = pd.ID
					INNER JOIN pessoa pe1 ON pe1.ID = pd.consumidor_pessoa_ID
					LEFT JOIN endereco en1 ON en1.pessoa_ID = pe1.ID AND en1.tipo_endereco = 'C'
					INNER JOIN pessoa_juridica pe2 ON pe2.pessoa_ID = pi.estoque_lojista_pessoa_id
					LEFT JOIN endereco en2 ON en2.pessoa_ID = pe2.pessoa_ID AND en2.tipo_endereco = 'C' ";

		$where = false;

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= "tr.status_transacao = '4' ";

		if (!DataValidator::isEmpty($pesquisa->getPedido())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.ID = :ID ";
		}

		if (!DataValidator::isEmpty($pesquisa->getLojista())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getLojista()) ? "pi.estoque_lojista_pessoa_ID = :lojista " : "pe2.nome_fantasia LIKE _utf8 :lojista COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.data_pedido BETWEEN CONCAT(:dataDe, ' 00:00:00') AND CONCAT(:dataAte, ' 23:59:59') ";
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()) || !DataValidator::isEmpty($pesquisa->getCategoriaId()) || !DataValidator::isEmpty($pesquisa->getSubcategoriaId())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "EXISTS(
							SELECT * FROM produto_categoria pc
							WHERE pc.produto_ID = pi.estoque_produto_ID " . (!DataValidator::isEmpty($pesquisa->getModuloId()) ? "AND pc.modulo_ID = :moduloId " : "") . (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()) ? "AND pc.subcategoria_ID = :subcategoriaId " : "") . (!DataValidator::isEmpty($pesquisa->getCategoriaId()) ? "AND pc.categoria_ID = :categoriaId " : "") .
				") ";
		}

		if (!DataValidator::isEmpty($pesquisa->getProduto())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getProduto()) ? "pr.cod_produto LIKE :produto) " : "pr.nome_produto LIKE _utf8 :produto COLLATE utf8_unicode_ci) ";
		}

		$sql .= "ORDER BY pi.pedido_ID DESC, pi.estoque_lojista_pessoa_ID ASC;";

		$model = new PedidoModel();
		$query = $model->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($pesquisa->getPedido()))
			$query->bindValue(':ID', $pesquisa->getPedido(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getLojista()))
			if (is_numeric($pesquisa->getLojista()))
				$query->bindValue(':lojista', $pesquisa->getLojista(), PDO::PARAM_INT);
			else
				$query->bindValue(':lojista', "%" . $pesquisa->getLojista() . "%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			$query->bindValue(':dataDe', substr($pesquisa->getDataDe(), 6, 4) . "-" . substr($pesquisa->getDataDe(), 3, 2) . "-" . substr($pesquisa->getDataDe(), 0, 2), PDO::PARAM_STR);
			$query->bindValue(':dataAte', substr($pesquisa->getDataAte(), 6, 4) . "-" . substr($pesquisa->getDataAte(), 3, 2) . "-" . substr($pesquisa->getDataAte(), 0, 2), PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()))
			$query->bindValue(':moduloId', $pesquisa->getModuloId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getCategoriaId()))
			$query->bindValue(':categoriaId', $pesquisa->getCategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()))
			$query->bindValue(':subcategoriaId', $pesquisa->getSubcategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getProduto()))
			if (is_numeric($pesquisa->getProduto()))
				$query->bindValue(':produto', $pesquisa->getProduto() . "%", PDO::PARAM_STR);
			else
				$query->bindValue(':produto', "%" . $pesquisa->getProduto() . "%", PDO::PARAM_STR);

		$query->execute();
		$pedidos = array();

		while ($linha = $query->fetchObject())
			$pedidos[] = array(
				"pedido_ID" => $linha->pedido_ID,
				"produto_ID" => $linha->estoque_produto_ID,
				"lojista_ID" => $linha->estoque_lojista_pessoa_ID,
				"qtde" => $linha->qtde,
				"vl_unitario" => $linha->vl_unitario,
				"nome_produto" => $linha->nome_produto,
				"nome_fabricante" => $linha->nome_fabricante,
				"nome_marca" => $linha->nome_marca,
				"data_pedido" => date_create($linha->data_pedido, new DateTimeZone('Etc/GMT+3')),
				"status_pedido" => $linha->status_transacao,
				"nome_consumidor" => $linha->nome_consumidor,
				"cep_consumidor" => $linha->cep_consumidor,
				"bairro_consumidor" => $linha->bairro_consumidor,
				"nome_lojista" => $linha->nome_lojista,
				"cep_lojista" => $linha->cep_lojista,
				"bairro_lojista" => $linha->bairro_lojista
			);

		$sql = "SELECT
					pi.pedido_ID,
					pi.estoque_lojista_pessoa_ID,
					ROUND( SUM( pi.qtde * pi.vl_unitario ), 2) AS subtotal,
					MIN( pf.preco_frete ) AS preco_frete,
					MIN( pf.vl_taxas ) AS vl_taxas,
					MIN( pf.vl_comissao ) AS vl_comissao
				FROM pedido pd
					INNER JOIN pedido_item pi ON pi.pedido_ID = pd.ID
					INNER JOIN pedido_frete pf ON pf.pedido_ID = pd.ID AND pf.lojista_pessoa_ID = pi.estoque_lojista_pessoa_ID
					INNER JOIN produto pr ON pr.ID = pi.estoque_produto_ID
					INNER JOIN transacao tr ON tr.pedido_ID = pd.ID
					INNER JOIN pessoa pe1 ON pe1.ID = pd.consumidor_pessoa_ID
					INNER JOIN pessoa_juridica pe2 ON pe2.pessoa_ID = pi.estoque_lojista_pessoa_id ";

		$where = false;

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= "tr.status_transacao = '4' ";

		if (!DataValidator::isEmpty($pesquisa->getPedido())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.ID = :ID ";
		}

		if (!DataValidator::isEmpty($pesquisa->getLojista())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getLojista()) ? "pi.estoque_lojista_pessoa_ID = :lojista " : "pe2.nome_fantasia LIKE _utf8 :lojista COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "pd.data_pedido BETWEEN CONCAT(:dataDe, ' 00:00:00') AND CONCAT(:dataAte, ' 23:59:59') ";
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()) || !DataValidator::isEmpty($pesquisa->getCategoriaId()) || !DataValidator::isEmpty($pesquisa->getSubcategoriaId())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= "EXISTS(
							SELECT * FROM produto_categoria pc
							WHERE pc.produto_ID = pi.estoque_produto_ID " . (!DataValidator::isEmpty($pesquisa->getModuloId()) ? "AND pc.modulo_ID = :moduloId " : "") . (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()) ? "AND pc.subcategoria_ID = :subcategoriaId " : "") . (!DataValidator::isEmpty($pesquisa->getCategoriaId()) ? "AND pc.categoria_ID = :categoriaId " : "") .
				") ";
		}

		if (!DataValidator::isEmpty($pesquisa->getProduto())) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= is_numeric($pesquisa->getProduto()) ? "pr.cod_produto LIKE :produto) " : "pr.nome_produto LIKE _utf8 :produto COLLATE utf8_unicode_ci) ";
		}

		$sql .= "GROUP BY pi.pedido_ID, pi.estoque_lojista_pessoa_ID
					ORDER BY pi.pedido_ID DESC, pi.estoque_lojista_pessoa_ID ASC;";

		$model = new PedidoModel();
		$query = $model->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($pesquisa->getPedido()))
			$query->bindValue(':ID', $pesquisa->getPedido(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getLojista()))
			if (is_numeric($pesquisa->getLojista()))
				$query->bindValue(':lojista', $pesquisa->getLojista(), PDO::PARAM_INT);
			else
				$query->bindValue(':lojista', "%" . $pesquisa->getLojista() . "%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pesquisa->getDataDe()) && !DataValidator::isEmpty($pesquisa->getDataAte())) {
			$query->bindValue(':dataDe', substr($pesquisa->getDataDe(), 6, 4) . "-" . substr($pesquisa->getDataDe(), 3, 2) . "-" . substr($pesquisa->getDataDe(), 0, 2), PDO::PARAM_STR);
			$query->bindValue(':dataAte', substr($pesquisa->getDataAte(), 6, 4) . "-" . substr($pesquisa->getDataAte(), 3, 2) . "-" . substr($pesquisa->getDataAte(), 0, 2), PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($pesquisa->getModuloId()))
			$query->bindValue(':moduloId', $pesquisa->getModuloId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getCategoriaId()))
			$query->bindValue(':categoriaId', $pesquisa->getCategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getSubcategoriaId()))
			$query->bindValue(':subcategoriaId', $pesquisa->getSubcategoriaId(), PDO::PARAM_INT);

		if (!DataValidator::isEmpty($pesquisa->getProduto()))
			if (is_numeric($pesquisa->getProduto()))
				$query->bindValue(':produto', $pesquisa->getProduto() . "%", PDO::PARAM_STR);
			else
				$query->bindValue(':produto', "%" . $pesquisa->getProduto() . "%", PDO::PARAM_STR);

		$query->execute();
		$lojistas = array();

		while ($linha = $query->fetchObject())
			$lojistas[] = array(
				"pedido_ID" => $linha->pedido_ID,
				"lojista_ID" => $linha->estoque_lojista_pessoa_ID,
				"subtotal" => $linha->subtotal,
				"preco_frete" => $linha->preco_frete,
				"vl_taxas" => $linha->vl_taxas,
				"vl_comissao" => $linha->vl_comissao
			);

		return array('pedidos' => $pedidos, 'lojistas' => $lojistas);
	}
}
