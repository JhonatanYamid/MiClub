<?php
ini_set('display_errors', 1); 
include("../config.inc.php");
ini_set('display_errors', 1); 
//json general para contenido de app mobile

$json = new Services_JSON;

$arrayGeneral         = array();
$arrayRecetaDestacada = array();
$arrayTipo            = array();
$arrayTipoReceta      = array();
$arrayRecetaTipo      = array();
$arrayMarcas          = array();
$arrayCategoria       = array();
$arrayCategoria1      = array();
$arrayCategoria2      = array();
$arrayProducto        = array();

//Datos Recetas
$SqlTipo = "SELECT * FROM REC_Tipo WHERE Publicar = 'S'";
$QryTipo = $dbo->query( $SqlTipo );
$NumTipo = $dbo->rows( $QryTipo );

if( $NumTipo > 0 )
{
	while( $Tipo = $dbo->fetchArray( $QryTipo ) )	
	{		
		//obtener secciones
		$SqlTipoReceta = "SELECT * FROM REC_TipoReceta WHERE IDTipo = '" . $Tipo["IDTipo"] . "'";
		$QryTipoReceta = $dbo->query( $SqlTipoReceta );
		$NumTipoReceta = $dbo->rows( $QryTipoReceta );
		if( $NumTipoReceta > 0 )
		{
			$i = 0;
			while( $TipoReceta = $dbo->fetchArray( $QryTipoReceta ) )	
			{
				$arrayTipoReceta[$i] = $TipoReceta;
				$i++;
					
			}
			
			foreach( $arrayTipoReceta as $key => $value )
			{
				$SqlReceta = "SELECT * FROM Receta WHERE IDReceta = '" . $value["IDReceta"] . "' AND Publicar = 'S'";
				$QryReceta = $dbo->query( $SqlReceta );
				$NumReceta = $dbo->rows( $QryReceta );
				if( $NumReceta > 0 )
				{
					$Receta = $dbo->fetchArray( $QryReceta );
					$Receta["Foto"] = IMGRECETA_ROOT . $Receta["Foto"];
					$arrayReceta[$Receta["IDReceta"]] = $Receta;
					$Tipo["Receta"][$Receta["IDReceta"]] = $arrayReceta[$Receta["IDReceta"]];
				}

				
			}
		}
		else
		{
			$arrayRecetaTipo = NULL;
		}
				
		$arrayRecetaTipo[$Tipo["IDTipo"]] = $Tipo;
		
	}	
}
else
{
	$arrayTipo = NULL;
	
}


//datos Home
$SqlFotoHome = "SELECT MAX( FechaTrEd ), MAX(FechaTrCr) from Receta";
$QryFotoHome = $dbo->query( $SqlFotoHome );
$NumFotoHome = $dbo->rows( $QryFotoHome );
if( $NumFotoHome > 0 )
{
	$FotoHome = $dbo->fetchArray( $QryFotoHome );
	//$FotoHome["BannerFile"] = IMGBANNER_ROOT . $FotoHome["BannerFile"]; 
}

//marcas
$SqlMarcas = "SELECT * FROM PRODUCTO_Categoria WHERE IDPadre = '0' AND Publicar = 'S'";
$QryMarcas = $dbo->query( $SqlMarcas );
$NumMarcas = $dbo->rows( $QryMarcas );
while( $Marcas = $dbo->fetchArray( $QryMarcas ) )
{

	//tipos de producto por marca
	$SqlTipoCategoria = "SELECT * FROM PRODUCTO_Categoria WHERE IDPadre = '" . $Marcas["IDCategoria"] . "' AND Publicar = 'S'";
	$QryTipoCategoria = $dbo->query( $SqlTipoCategoria );
	$NumTipoCategoria = $dbo->rows( $QryTipoCategoria );
	if( $NumTipoCategoria > 0 )
	{
		while( $TipoCategoria = $dbo->fetchArray( $QryTipoCategoria ) )
		{
			
			$TipoCategoria["Foto"] = IMGCATEGORIA_ROOT . $TipoCategoria["Foto"];
			$arrayCategoria[$TipoCategoria["IDCategoria"]] = $TipoCategoria;
			$Marcas["Categoria"][$TipoCategoria["IDCategoria"]] = $arrayCategoria[$TipoCategoria["IDCategoria"]];	
			$file = file_get_contents($TipoCategoria["Foto"]);
		//	$TipoCategoria["Foto"] = base64_encode($file);
		//	echo "insert into categorias (id, nombre, foto, idmarca, idpadre) values (".$TipoCategoria[IDCategoria].", '".$TipoCategoria[Nombre]."', '".$TipoCategoria[Foto]."', ".$Marcas["IDCategoria"].", '0');";
			//subcategoria
			$SqlSubCategoria = "SELECT * FROM PRODUCTO_Categoria WHERE IDPadre = '" . $TipoCategoria["IDCategoria"] . "' AND Publicar = 'S' LIMIT 10";
			$QrySubCategoria = $dbo->query( $SqlSubCategoria );
			$NumSubCategoria = $dbo->rows( $QrySubCategoria );
			if( $NumSubCategoria > 0 )//si tiene subcategorias, se agregan al arreglo
			{
				while( $SubCategoria = $dbo->fetchArray( $QrySubCategoria ) )
				{
					$SubCategoria["Foto"] = IMGCATEGORIA_ROOT . $SubCategoria["Foto"];
					$arrayCategoria1[$SubCategoria["IDCategoria"]] = $SubCategoria;
				//	$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Subcategoria"][$SubCategoria["IDCategoria"]] = $arrayCategoria1[$SubCategoria["IDCategoria"]];
				$file = file_get_contents($SubCategoria["Foto"]);
				//$SubCategoria["Foto"] = base64_encode($file);
				//echo "insert into categorias (id, nombre, foto, idmarca, idpadre) values (".$SubCategoria[IDCategoria].", '".$SubCategoria[Nombre]."', '".$SubCategoria[Foto]."', ".$Marcas["IDCategoria"].", '".$SubCategoria[IDPadre]."');";
					//subcategoria1
					$SqlSubCategoria1 = "SELECT * FROM PRODUCTO_Categoria WHERE IDPadre = '" . $SubCategoria["IDCategoria"] . "' AND Publicar = 'S' LIMIT 10";
					$QrySubCategoria1 = $dbo->query( $SqlSubCategoria1 );
					$NumSubCategoria1 = $dbo->rows( $QrySubCategoria1 );
					if( $NumSubCategoria1 > 0 )//si hay un 3er nivel
					{	
						while( $SubCategoria1 = $dbo->fetchArray( $QrySubCategoria1 ) )
						{
							$SubCategoria1["Foto"] = IMGCATEGORIA_ROOT . $SubCategoria1["Foto"];
							$arrayCategoria2[$SubCategoria1["IDCategoria"]] = $SubCategoria1;
							$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Subcategoria"][$SubCategoria["IDCategoria"]]["Subcategoria"][$SubCategoria1["IDCategoria"]] = $arrayCategoria2[$SubCategoria1["IDCategoria"]];
								$file = file_get_contents($SubCategoria1["Foto"]);
							//	$SubCategoria1["Foto"] = base64_encode($file);
							//	echo "insert into categorias (id, nombre, foto, idmarca, idpadre) values (".$SubCategoria1[IDCategoria].", '".$SubCategoria1[Nombre]."', '".$SubCategoria1[Foto]."', ".$Marcas["IDCategoria"].", '".$SubCategoria1[IDPadre]."');";
							//el 3er nivel es el maximo nivel en la subcategorias de las marcas, entonces buscar productos
							$SqlProducto = "SELECT * FROM Producto WHERE IDCategoria = '" . $SubCategoria1["IDCategoria"] . "' AND Publicar = 'S' ORDER BY Nuevo ASC";
							$QryProducto = $dbo->query( $SqlProducto );
							$NumProducto = $dbo->rows( $QryProducto );
							if( $NumProducto > 0 )
							{
								while( $Producto = $dbo->fetchArray( $QryProducto ) )
								{
									//$Producto["IDCategoria"] = $TipoCategoria["IDCategoria"];
									$Producto["Foto"] = IMGPRODUCTO_ROOT . $Producto["Foto"];
									$arrayProducto[$Producto["IDProducto"]] = $Producto;
									$file = file_get_contents($Producto["Foto"]);

									//$Producto["Foto"] = base64_encode($file);
									$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Producto"][$Producto["IDProducto"]] = $arrayProducto[$Producto["IDProducto"]];
								//	echo "insert into productos (id, nombre, foto, descripcion, codigo, idcategoria) values (".$Producto[IDProducto].", '".$Producto[Nombre]."', '".$Producto[Foto]."', '".$Producto[Descripcion]."', '".$Producto[Codigo]."', ".$Producto[IDCategoria].");";	$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Subcategoria"][$SubCategoria["IDCategoria"]]["Subcategoria"][$SubCategoria1["IDCategoria"]]["Producto"][$Producto["IDProducto"]] = $arrayProducto[$Producto["IDProducto"]];
								}
							}
						}
						
					}
					else // si no hay mas de 2 niveles, buscar productos de la subcategoria
					{
						$SqlProducto = "SELECT * FROM Producto WHERE IDCategoria = '" . $SubCategoria["IDCategoria"] . "' AND Publicar = 'S' ORDER BY Nuevo ASC";
						$QryProducto = $dbo->query( $SqlProducto );
						$NumProducto = $dbo->rows( $QryProducto );
						if( $NumProducto > 0 )
						{
							while( $Producto = $dbo->fetchArray( $QryProducto ) )
							{
								//$Producto["IDCategoria"] = $TipoCategoria["IDCategoria"];
								$Producto["Foto"] = IMGPRODUCTO_ROOT . $Producto["Foto"];
								$file = file_get_contents($Producto["Foto"]);

								//$Producto["Foto"] = base64_encode($file);
								$arrayProducto[$Producto["IDProducto"]] = $Producto;
								$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Producto"][$Producto["IDProducto"]] = $arrayProducto[$Producto["IDProducto"]];
							//	echo "insert into productos (id, nombre, foto, descripcion, codigo, idcategoria) values (".$Producto[IDProducto].", '".$Producto[Nombre]."', '".$Producto[Foto]."', '".$Producto[Descripcion]."', '".$Producto[Codigo]."', ".$Producto[IDCategoria].");";
								$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Subcategoria"][$SubCategoria["IDCategoria"]]["Producto"][$Producto["IDProducto"]] = $arrayProducto[$Producto["IDProducto"]];
							}
						}
					}			
				}				
			}
			else //si no tiene, se buscan los productos asoaciados a la categoria
			{
			
				$SqlProducto = "SELECT * FROM Producto WHERE IDCategoria = '" . $TipoCategoria["IDCategoria"] . "' AND Publicar = 'S' ORDER BY Nuevo ASC";
				$QryProducto = $dbo->query( $SqlProducto );
				$NumProducto = $dbo->rows( $QryProducto );
				if( $NumProducto > 0 )
				{
					while( $Producto = $dbo->fetchArray( $QryProducto ) )
					{
						$Producto["Foto"] = IMGPRODUCTO_ROOT . $Producto["Foto"];
						$file = file_get_contents($Producto["Foto"]);

					//	$Producto["Foto"] = base64_encode($file);
					
						$arrayProducto[$Producto["IDProducto"]] = $Producto;
					//	echo "insert into productos (id, nombre, foto, descripcion, codigo, idcategoria) values (".$Producto[IDProducto].", '".$Producto[Nombre]."', '".$Producto[Foto]."', '".$Producto[Descripcion]."', '".$Producto[Codigo]."', ".$Producto[IDCategoria].");";
						$Marcas["Categoria"][$TipoCategoria["IDCategoria"]]["Producto"][$Producto["IDProducto"]] = $arrayProducto[$Producto["IDProducto"]];
					}
				}
			}			
		}
		$Marcas["Foto"] = IMGCATEGORIA_ROOT . $Marcas["Foto"];
		$arrayMarcas[$Marcas["IDCategoria"]] = $Marcas;		
	}
}

$arrayTipo = $arrayRecetaTipo;

$arrayGeneral["Home"] = $FotoHome;
$arrayGeneral["TipoReceta"] = $arrayTipo;
$arrayGeneral["Marcas"] = $arrayMarcas;

//visualizamos el array/json

echo $json->encode( $arrayGeneral );

?>