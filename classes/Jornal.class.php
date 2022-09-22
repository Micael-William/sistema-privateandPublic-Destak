<?php
	class Jornal{
		private $id;
		private $nome;
		private $status; //padrão, comum
		private $ativo;
		private $data_confirmacao;
		private $data_alteracao;
		private $data_entrada;
		private $nome_representante;
		private $contato_representante;
		private $fechamento;
		private $composicao;
		private $estado_periodo;
		//propriedade e não objeto
		private $observacoes;
		
		private $endereco;
		private $usuario;
		private $custo;
		
		private $telefones;
		private $secretarias;
		private $emails;
		private $periodos;	
		private $cidades;	
		private $alertas;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getNome(){
			return $this->nome;
		}
		
		public function setNome($nome){
			$this->nome = $nome;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function getStatusDesc(){
			if( $this->status == 'P') return 'Padrão';
			if( $this->status == 'C') return 'Comum';
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getAtivo(){
			return $this->ativo;
		}
		
		public function getAtivoDesc(){
			return $this->ativo == 'A' ? 'Ativo' : 'Inativo';
		}
		
		public function setAtivo($ativo){
			$this->ativo = $ativo;
		}
		
		public function getDataConfirmacao(){
			return $this->data_confirmacao;
		}
		
		public function setDataConfirmacao($data_confirmacao){
			$this->data_confirmacao = $data_confirmacao;
		}
		
		public function getDataAlteracao(){
			return $this->data_alteracao;
		}
		
		public function setDataAlteracao($data_alteracao){
			$this->data_alteracao = $data_alteracao;
		}
		
		public function getDataEntrada(){
			return $this->data_entrada;
		}
		
		public function setDataEntrada($data_entrada){
			$this->data_entrada = $data_entrada;
		}
		
		public function getNomeRepresentante(){
			return $this->nome_representante;
		}
		
		public function setNomeRepresentante($nome_representante){
			$this->nome_representante = $nome_representante;
		}
		
		public function getContatoRepresentante(){
			return $this->contato_representante;
		}
		
		public function setContatoRepresentante($contato_representante){
			$this->contato_representante = $contato_representante;
		}		
		
		public function getFechamento(){
			return $this->fechamento;
		}
		
		public function setFechamento($fechamento){
			$this->fechamento = $fechamento;
		}
		
		public function getComposicao(){
			return $this->composicao;
		}
		
		public function setComposicao($composicao){
			$this->composicao = $composicao;
		}
		
		public function getObservacoes(){
			return $this->observacoes;
		}
		
		public function setObservacoes($observacoes){
			$this->observacoes = $observacoes;
		}		
		
		public function getEstadoPeriodo(){
			return $this->estado_periodo;
		}
		
		public function setEstadoPeriodo($estado_periodo){
			$this->estado_periodo = $estado_periodo;
		}		
		
		public function getEndereco(){
			return $this->endereco;
		}
		
		public function setEndereco($endereco){
			$this->endereco = $endereco instanceof Endereco ? $endereco : null;
		}
		
		public function getUsuario(){
			return $this->usuario;
		}
		
		public function setUsuario($usuario){
			$this->usuario = $usuario instanceof Usuario ? $usuario : null;
		}
		
		public function getCusto(){
			return $this->custo;
		}
		
		public function setCusto($custo){
			$this->custo = $custo instanceof Custo ? $custo : null;
		}		
		
		public function getSecretarias() {
			return $this->secretarias;
		}		
	
		public function setSecretarias( $secretarias ) {
			$this->secretarias = null;
			
			if (is_array( $secretarias )) {
				$isSec = true;
				
				foreach ($secretarias as $s){
					if (!$s instanceof Secretaria) {
						$isSec = false;
						break;
					}
				}
				
				if ($isSec)
					$this->secretarias = $secretarias;
			}
		}
		
		public function setSecretaria( $secretaria ){
			if (!isset($this->secretarias) or is_null($this->secretarias))
				$this->secretarias = array();
	
			$this->secretarias[] = $secretaria;
		}
		
		public function getEmails() {
			return $this->emails;
		}		
	
		public function setEmails( $emails ) {
			$this->emails = null;
			
			if (is_array( $emails )) {
				$isEmail = true;
				
				foreach ($emails as $e){
					if (!$e instanceof Email) {
						$isEmail = false;
						break;
					}
				}
				
				if ($isEmail)
					$this->emails = $emails;
			}
		}
		
		public function setEmail( $email ){
			if (!isset($this->emails) or is_null($this->emails))
				$this->emails = array();
	
			$this->emails[] = $email;
		}
		
		public function getPeriodos() {
			return $this->periodos;
		}		
	
		public function setPeriodos( $periodos ) {
			$this->periodos = null;
			
			if (is_array( $periodos )) 
				$this->periodos = $periodos;
		}
		
		public function setPeriodo( $periodo ){
			if (!isset($this->periodos) or is_null($this->periodos))
				$this->periodos = array();
	
			$this->periodos[] = $periodo;
		}
		
		public function getTelefones() {
			return $this->telefones;
		}		
	
		public function setTelefones( $telefones ) {
			$this->telefones = null;
			
			if (is_array( $telefones )) {
				$isTel = true;
				
				foreach ($telefones as $t){
					if (!$t instanceof Telefone) {
						$isTel = false;
						break;
					}
				}
				
				if ($isTel)
					$this->telefones = $telefones;
			}
		}
		
		public function setTelefone( $telefone ){
			if (!isset($this->telefones) or is_null($this->telefones))
				$this->telefones = array();
	
			$this->telefones[] = $telefone;
		}
		
		public function getAlertas() {
			return $this->alertas;
		}		
	
		public function setAlertas( $alertas ) {
			$this->alertas = null;
			
			if (is_array( $alertas ))				
				$this->alertas = $alertas;
		}
		
		public function setAlerta( $alerta ){
			if (!isset($this->alertas) or is_null($this->alertas))
				$this->alertas = array();
	
			$this->alertas[] = $alerta;
		}
		
		public function getCidades() {
			return $this->cidades;
		}		
	
		public function setCidades( $cidades ) {
			$this->cidades = null;
			
			if (is_array( $cidades )) {
				$isCidade = true;
				
				foreach ($cidades as $c){
					if (!$c instanceof Cidade) {
						$isCidade = false;
						break;
					}
				}
				
				if ($isCidade)
					$this->cidades = $cidades;
			}
		}
		
		public function setCidade( $cidade ){
			if (!isset($this->cidades) or is_null($this->cidades))
				$this->cidades = array();
	
			$this->cidades[] = $cidade;
		}
				
	}
?>