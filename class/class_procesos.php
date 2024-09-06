<?php

	class Proceso{

        // FUNCION LISTADO DE AREAS
        public static function ListadoAreas(){
            try{

                $Buscar="SELECT fld_id as id, fld_nombre as nombre FROM tbl_areas ORDER BY fld_nombre ASC";
                $cmd= Database::getInstance()->getDb()->prepare($Buscar);
				$cmd->execute();
                $row=$cmd->fetchAll(PDO::FETCH_ASSOC);
                $Cantidad=$cmd->rowCount();

                return array('Validacion'=>'OK', 'Row'=>$row, 'Cantidad'=>$Cantidad);

            }catch(PDOException $e){
				return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
			}
        }// END FUNCION 

        // FUNCION LISTADO DE ROLES
        public static function ListadoRoles(){
            try{

                $Buscar="SELECT fld_id as id, fld_nombre as nombre FROM tbl_roles ORDER BY fld_nombre ASC";
                $cmd= Database::getInstance()->getDb()->prepare($Buscar);
				$cmd->execute();
                $row=$cmd->fetchAll(PDO::FETCH_ASSOC);
                $Cantidad=$cmd->rowCount();

                return array('Validacion'=>'OK', 'Row'=>$row, 'Cantidad'=>$Cantidad);

            }catch(PDOException $e){
				return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
			}
        }// END FUNCION 

        public static function validateField($value, $pattern, $errorMessage) {
            if (!preg_match($pattern, $value)) {
                return $errorMessage;
            }
            return null;
        }

        // FUNCION VALIDACION DE DATOS
        public static function ValidationData($nombre, $correo, $sexo, $area, $descripcion, $roles){
            try{

                $nombreError = self::validateField($nombre, '/^[a-zA-ZÀ-ÿ\s]+$/', "El nombre solo debe contener letras y espacios.");
                if ($nombreError) {
                    return array('Validacion'=>'Error', 'Mensaje'=>$nombreError);
                }

                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    return array('Validacion'=>'Error', 'Mensaje'=>"Por favor, ingresa un correo electrónico valido.");
                }

                // Validar sexo (debe ser M o F)
                if (!in_array($sexo, ['M', 'F'])) {
                    return array('Validacion'=>'Error', 'Mensaje'=>"Por favor, selecciona un sexo valido.");
                }

                if (empty($area)) {
                    return array('Validacion'=>'Error', 'Mensaje'=>"Por favor, selecciona un área.");
                }

                if (strlen($descripcion) < 10) {
                    return array('Validacion'=>'Error', 'Mensaje'=>"La descripción debe tener al menos 10 caracteres.");
                }

                if (empty($roles)) {
                    return array('Validacion'=>'Error', 'Mensaje'=>"Por favor, selecciona al menos un rol.");
                }

                return array('Validacion'=>'OK');

            }catch(PDOException $e){
				return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
			}

        }// END FUNCIO 

        // FUNCION AGREGAR EMPLEADO
        public static function AddEmpleado($nombre, $correo, $sexo, $area, $descripcion, $boletin, $roles){
			try{

                // VALIDACION DE LOS DATOS ENVIAOS
                $VALIDACION=self::ValidationData($nombre, $correo, $sexo, $area, $descripcion, $roles);

                if($VALIDACION['Validacion']=='OK'){

                    // Validamos que el correo no exista
                    $BuscarEmail="SELECT fld_email FROM tbl_empleados WHERE fld_email=?";
                    $cmd= Database::getInstance()->getDb()->prepare($BuscarEmail);
                    $cmd->execute(array($correo));
                    $CantidadEmail=$cmd->rowCount();

                    if($CantidadEmail==0){

                        // Registramos el nuevo empleado
                        $insertSQL ="INSERT INTO tbl_empleados (fld_nombre, fld_email, fld_sexo, fld_IDarea, fld_boletin, fld_descripcion, fld_registro) VALUES (?,?,?,?,?,?,now())";
                        $sql= Database::getInstance()->getDb()->prepare($insertSQL);
                        $sql->execute(array($nombre, $correo, $sexo, $area, $boletin, $descripcion)); 

                        // Consultamos el ultimos ID insertado
                        $Consultar="SELECT LAST_INSERT_ID() FROM tbl_empleados WHERE fld_email=?";
                        $cmd = Database::getInstance()->getDb()->prepare($Consultar);
                        $cmd->execute(array($correo));
                        $IDempleado=$cmd->fetchColumn();

                        // Guardamos los roles
                        $roles=explode(',',$roles);
                        foreach($roles as $key){
                            $insertSQLR ="INSERT INTO tbl_empleados_rol (fld_IDempleado, fld_IDrol) VALUES (?,?)";
                            $sqlR= Database::getInstance()->getDb()->prepare($insertSQLR);
                            $sqlR->execute(array($IDempleado, $key)); 
                        }


                        return array('Validacion'=>'OK');

                    }else{

                        return array('Validacion'=>'Error', 'Mensaje'=>'El correo electrónico ya existe. Intenta con otro.');
                    }
                }else{

                    return $VALIDACION;
                }

			}catch(PDOException $e){
				return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
			}
		}//End Funcion

        // FUNCION BUSCAR LISTADO EMPLEADOS
		public static function ListadoEmpleados($buscar, $page){
			try{

				$onclic="load";
				include 'pagination.php';

				if($buscar!=''){
					$WHERE=" WHERE e.fld_nombre LIKE '%".$buscar."%' ";
				}else{
					$WHERE="";
				}

				$per_page = 10; // registros que mostrar
				$adjacents  = 4;
				$offset = ($page - 1) * $per_page;

				$Busqueda="SELECT count(*) AS numrows FROM tbl_empleados e 
                INNER JOIN tbl_areas a ON e.fld_IDarea=a.fld_id 
                $WHERE
                ORDER BY e.fld_id DESC";
				$consulta= Database::getInstance()->getDb()->prepare($Busqueda);
				$consulta->execute();
				$row=$consulta->fetch(PDO::FETCH_ASSOC);
				$numrows = $row['numrows'];
				$total_pages = ceil($numrows/$per_page);
				$reload = 'index.html';

                $navegacion='<tr><td colspan=4><p>Mostrando un total de '.$numrows.' registos</p></td>
					<td colspan=3><span class="pull-right">'. paginate($reload, $page, $total_pages, $adjacents,$onclic).'</span></td></tr>';

				$Buscar="SELECT 
                e.fld_id as id, 
                e.fld_nombre as nombre, 
                e.fld_email as email, 
                e.fld_sexo as sexo, 
                a.fld_nombre as area,
                e.fld_boletin as boletin
                FROM tbl_empleados e 
                INNER JOIN tbl_areas a ON e.fld_IDarea=a.fld_id 
                $WHERE
                ORDER BY e.fld_id DESC LIMIT $offset,$per_page";
				$cmd= Database::getInstance()->getDb()->prepare($Buscar);
				$cmd->execute();
				$rowBu=$cmd->fetchAll(PDO::FETCH_ASSOC);

                return array('Validacion'=>'OK', 'Row'=>$rowBu, 'Cantidad'=>$numrows, 'Navegacion'=>$navegacion);


			}catch(PDOException $e){
				return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
			}
		}// END FUNCION

        // FUNCION BUSCAR INFORMACION DE UN EMPLEADO ESPECIFICO
        public static function InformacionEmpleado($id){
            try{

                // Info del empleado
                $BuscarInfo="SELECT 
                fld_nombre as nombre, 
                fld_email as email, 
                fld_sexo as sexo, 
                fld_IDarea as area,
                fld_boletin as boletin,
                fld_descripcion as descripcion
                FROM tbl_empleados 
                WHERE fld_id=?";
                $cmd= Database::getInstance()->getDb()->prepare($BuscarInfo);
				$cmd->execute(array($id));
				$row=$cmd->fetch(PDO::FETCH_ASSOC);

                // Info roles asignados
                $ListadoRol="SELECT fld_IDrol as id FROM tbl_empleados_rol WHERE fld_IDempleado=?";
                $sql= Database::getInstance()->getDb()->prepare($ListadoRol);
				$sql->execute(array($id));
				$roles=$sql->fetchAll(PDO::FETCH_ASSOC);

				return array('Validacion'=>'OK', 'row'=>$row, 'roles'=>$roles);

            }catch(PDOException $e){
                return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
            }
        }// END FUNCION

         // FUNCION ACTUALIZAR INFORMACION DE EMPLEADO
         public static function UpdateEmpleado($id, $nombre, $correo, $sexo, $area, $descripcion, $boletin, $roles){
            try{

                // VALIDACION DE LOS DATOS ENVIAOS
                $VALIDACION=self::ValidationData($nombre, $correo, $sexo, $area, $descripcion, $roles);

                if($VALIDACION['Validacion']=='OK'){
                    
                    // Validamos que el correo no exista
                    $BuscarEmail="SELECT fld_email FROM tbl_empleados WHERE fld_email=? AND fld_id!=?";
                    $cmd= Database::getInstance()->getDb()->prepare($BuscarEmail);
                    $cmd->execute(array($correo, $id));
                    $CantidadEmail=$cmd->rowCount();

                    if($CantidadEmail==0){

                        $UPDATE ="UPDATE tbl_empleados SET fld_nombre=?, fld_email=?, fld_sexo=?, fld_IDarea=?, fld_boletin=?, fld_descripcion=?, fld_update=now() WHERE fld_id=?";
                        $sql= Database::getInstance()->getDb()->prepare($UPDATE);
                        $sql->execute(array($nombre, $correo, $sexo, $area, $boletin, $descripcion, $id));

                        // Actualizamos los roles
                        $DELETE="DELETE FROM tbl_empleados_rol WHERE fld_IDempleado=?";
                        $sql= Database::getInstance()->getDb()->prepare($DELETE);
                        $sql->execute(array($id));

                        $roles=explode(',',$roles);
                        foreach($roles as $rol){
                            $INSERT="INSERT INTO tbl_empleados_rol (fld_IDempleado, fld_IDrol) VALUES (?,?)";
                            $sql= Database::getInstance()->getDb()->prepare($INSERT);
                            $sql->execute(array($id, $rol));
                        }

                        return array('Validacion'=>'OK');

                    }else{

                        return array('Validacion'=>'Error', 'Mensaje'=>'El correo electrónico ya existe. Intenta con otro.');
                    }
                }else{
                    return $VALIDACION;
                }

            }catch(PDOException $e){
                return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
            }
        }// END FUNCION

        // ** FUNION ELIMINAR EMPLEADO
		public static function DeleteEmpleado($id){
			try{

				$Eliminar ="DELETE FROM tbl_empleados WHERE fld_id=?";
				$cmd= Database::getInstance()->getDb()->prepare($Eliminar);
				$cmd->execute(array($id)); 

				return array('Validacion'=>'OK');

			}catch(PDOException $e){
				return array('Validacion'=>'Error', 'Mensaje'=>'Error '.$e->getMessage());
			}
		}// END FUNCION 

    }// END CLASS
?>