<?php

	header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');
	require('database.php');
	require_once('../class/class_procesos.php');

    if(isset($_POST['action'])){

        if($_POST['action']=='AddEmpleado'){
			Proceso_AddEmpleado();

		}else if($_POST['action']=='ListadoEmpleados'){
			Proceso_ListadoEmpleados();

		}else if($_POST['action']=='InformacionEmpleado'){
			Proceso_InformacionEmpleado();

		}else if($_POST['action']=='UpdateEmpleado'){
			Proceso_UpdateEmpleado();

		}else if($_POST['action']=='DeleteEmpleado'){
			Proceso_DeleteEmpleado();

		}else{
            Accesso_Denegado();
        }
    }else{
		Accesso_Denegado();
	}

    function Accesso_Denegado(){

		print json_encode(array(
			"estado" => 2,
			"mensaje" => "Acceso Denegado!"
		));
	}

    function Proceso_AddEmpleado(){
		foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';"; 
			eval($asignacion); 
		}

		$class=Proceso::AddEmpleado($nombre, $correo, $sexo, $area, $descripcion, $boletin, $roles);

		if($class){
			$datos["estado"] = 1;
			$datos["datos"] = $class;
			print json_encode($datos);

		}else{
			print json_encode(array(
				"estado" => 2,
				"mensaje" => "Ocurrió un error al momento de realizar la búsqueda"
			));
		}
	}

    function Proceso_ListadoEmpleados(){

        foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';"; 
			eval($asignacion); 
		}

		$class=Proceso::ListadoEmpleados($buscar, $page);

		if($class){
			$datos["estado"] = 1;
			$datos["datos"] = $class;
			print json_encode($datos);

		}else{
			print json_encode(array(
				"estado" => 2,
				"mensaje" => "Ocurrió un error al momento de realizar la búsqueda"
			));
		}
	}

    function Proceso_InformacionEmpleado(){
        foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';"; 
			eval($asignacion); 
		}

		$class=Proceso::InformacionEmpleado($id);


		if($class){
			$datos["estado"] = 1;
			$datos["datos"] = $class;
			print json_encode($datos);

		}else{
			print json_encode(array(
				"estado" => 2,
				"mensaje" => "Ocurrió un error al momento de realizar la búsqueda"
			));
		}
    }

    function Proceso_UpdateEmpleado(){
        foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';"; 
			eval($asignacion); 
		}

		$class=Proceso::UpdateEmpleado($IDdatos, $nombre, $correo, $sexo, $area, $descripcion, $boletin, $roles);

		if($class){
			$datos["estado"] = 1;
			$datos["datos"] = $class;
			print json_encode($datos);

		}else{
			print json_encode(array(
				"estado" => 2,
				"mensaje" => "Ocurrió un error al momento de realizar la búsqueda"
			));
		}
    }

    function Proceso_DeleteEmpleado(){
        foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';"; 
			eval($asignacion); 
		}

		$class=Proceso::DeleteEmpleado($id);

		if($class){
			$datos["estado"] = 1;
			$datos["datos"] = $class;
			print json_encode($datos);

		}else{
			print json_encode(array(
				"estado" => 2,
				"mensaje" => "Ocurrió un error al momento de realizar la búsqueda"
			));
		}
    }

?>