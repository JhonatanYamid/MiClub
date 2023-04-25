<?php

class SIMArchivo{
	private $tabla;
	private $key;
	private $campos=array();
	private $file=array();
	private $origen;
	private $destino;
	private $nombrefile;
	private $typefile;
	private $sizefile;
	private $extenfile;
	static  $types = array(	"application/msword"=>".doc",
							"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>".docx",
							"application/vnd.ms-excel"=>".xls", 
							"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>".xlsx",
							"application/vnd.ms-powerpoint"=>".ppt",
							"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>".pptx",
							"application/pdf"=>".pdf",
							);
	function __construct( $file , $origen , $destino , $tabla , $campos ,$key){
		$this->tabla = $tabla;
		$this->key = $key;
		$this->campos = $campos;
		$this->file = $file;
		$this->origen = $origen;
		$this->destino = $destino;
	}
	
	public function NombreFile(){
		$this->nombrefile = $this->file[name];
	}
	
	public function TypeFile(){
		$this->typefile = $this->file[type];
	}
	
	public function SizeFile(){
		$this->sizefile = filesize($this->file[tmp_name])." bytes";
	}
	
	public function ExtenFile(){
		$this->extenfile = SIMArchivo::$types[$this->file[type]];	
	}
	
	public function SubeArchivo(){
		$dbo =& SIMDB::get();
		$this->NombreFile();
		$this->TypeFile();
		$this->SizeFile();
		$this->ExtenFile();
		if(!file_exists($this->destino.$this->nombrefile))
		{
			if(	move_uploaded_file($this->origen, $this->destino.$this->nombrefile))
			{
				$this->campos[NombreFile] = $this->nombrefile;
				$this->campos[TypeFile] = $this->typefile;
				$this->campos[SizeFile] = $this->sizefile;
				$this->campos[ExtenFile] = $this->extenfile;
				$dbo->insert( $this->campos , $this->tabla , $this->key );
				return "Documento Exitoso";	
			}
			else
				return "Problemas Al Cargar";
		}
		else
		{
		 return "Documento Existente";	
		}
	}	
}


?>