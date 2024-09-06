<?php
    require('ajax/database.php');
    require_once('class/class_procesos.php');

    $AREAS=Proceso::ListadoAreas();
    $ROLES=Proceso::ListadoRoles();
?>
<!DOCTYPE html>
<html lang="es">


<head>

 <meta charset="UTF-8">

 <meta name="viewport" content="width=device-width, initial-scale=1.0">

 <title>Crud - Jhonatan Facete</title>


 <link rel="stylesheet" href="assets/css/main/app.css">

 <link rel="stylesheet" href="assets/css/main/app-dark.css">

 <link rel="stylesheet" href="assets/css/style.css">

 <link rel="shortcut icon" href="assets/images/logo/favicon.svg" type="image/x-icon">
 <link rel="shortcut icon" href="assets/images/logo/favicon.png" type="image/png">

 <link rel="stylesheet" href="assets/css/shared/iconly.css">

 <link rel="stylesheet" href="assets/js/extensions/toastify/toastify.css">

 <link href="assets/js/extensions/fontawesome/css/all.css" rel="stylesheet">

</head>



<body>



 <div class="container py-5">



     <div class="page-heading">
         <h3><i class="fas fa-code"></i> CRUD - Jhonatan Facete</h3>
         <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
            <i class="far fa-sun"></i>
            <div class="form-check form-switch fs-6">
                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" >
                <label class="form-check-label" ></label>
            </div>
            <i class="fas fa-moon"></i>
        </div>
     </div>

     <div class="page-content">

         <section class="row">



             <!-- SESION PARA CONFIRMAR DATOS -->

             <div class="col-12 col-lg-12 ">

                 <div class="card">

                     <div class="card-header bg-success ">

                        <!-- Contenido -->
                        <div class="row">
                            <div class="col-md-5">
                                <label class="font-extrabold text-white">Buscar</label>
                                <div class="flex-grow-1">
                                    <div class="form-group position-relative  mb-0 has-icon-left">
                                        <input type="text" class="form-control" id="Buscar" placeholder="..." onKeyUp="load(1);">
                                        <div class="form-control-icon"><i class="fas fa-search"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 text-end pt-4">
                                <button type="button" class="btn btn-primary btn-fill btn-sm" onClick="add()">Agregar <i class="fas fa-user-plus"></i></button>
                            </div>
                        </div>
                        <!-- End contenido -->

                     </div>

                     <div class="card-body pt-4">
                        <!-- Body -->
                        
                        <img src="assets/images/svg-loaders/oval.svg" class="me-4 load" style="width: 3rem" alt="audio">

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-lg table-datos" id="DatosTable">
                                <thead class="thead-dark bg-primary text-white">
                                    <tr>
                                        <th class="text-start">#</th>
                                        <th class="text-start"><i class="fas fa-user"></i> Nombre </th>
                                        <th class="text-center"><i class="fas fa-envelope"></i> Email</th>
                                        <th class="text-center"><i class="fas fa-venus-mars"></i> Sexo</th>
                                        <th class="text-center"><i class="fas fa-briefcase"></i> Área</th>
                                        <th class="text-center"><i class="fas fa-envelope-open-text"></i> Boletín</th>
                                        <th class="text-center"><i class="bi bi-gear-wide"></i> Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>

                        <!-- END Body -->
                     </div>

                 </div>

             </div>

             <!-- END SESION PARA CONFIRMAR DATOS -->





         </section>

     </div>

 </div><!-- End Main Content-->

    <!-- MODAL DATOS -->
    <div class="modal fade text-left" id="ModalDatos" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel4" data-bs-backdrop="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white" id="TitleModal"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i></button>
                </div>
                <div class="modal-body">
                    <form id="FormDatos">
                        <!-- Contenido -->
                        <div class="row">

                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <labe for="nombre" class="font-extrabold">Nombre Completo <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre Completo del empleado" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <labe for="correo" class="font-extrabold">Correo Electrónico <small class="text-danger">*</small></label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <labe for="sexo" class="font-extrabold">Sexo <small class="text-danger">*</small></label><br>
                                    
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="sexo" id="sexo1" value="M">
                                        <label class="form-check-label" for="sexo1">Masculino</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="sexo" id="sexo2" value="F">
                                        <label class="form-check-label" for="sexo2">Femenino</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <labe for="area" class="font-extrabold">Área <small class="text-danger">*</small></label>
                                    <select class="form-select" name="area" id="area">
                                        <?php
                                            if($AREAS['Cantidad']>0){
                                                $OPTION='<option value="">[ SELECCIONAR ]</option>';
                                                foreach ($AREAS['Row'] as $key) {
                                                    $OPTION.='<option value="'.$key['id'].'">'.$key['nombre'].'</option>';
                                                }
                                            }else{
                                                $OPTION='<option value="">[ No hay áreas disponibles ]</option>';
                                            }
                                            echo $OPTION;
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 mb-1">
                                <div class="form-group">
                                    <labe for="correo" class="font-extrabold">Descripción <small class="text-danger">*</small></label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Descripción de la experiencia del empleado"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="boletin" id="boletin" value="1">
                                    <label class="form-check-label" for="boletin">Deseo recibir boletín informativo</label>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 form-group">
                                <label for="roles" class="font-extrabold">Roles <small class="text-danger">*</small></label>

                                <?php
                                    if($ROLES['Cantidad']>0){
                                        $OPTION='';
                                        foreach ($ROLES['Row'] as $key) {
                                            $OPTION.='<div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="'.$key['id'].'" name="roles[]" id="roles'.$key['id'].'">
                                                <label class="form-check-label" for="roles'.$key['id'].'">
                                                    '.$key['nombre'].'
                                                </label>
                                            </div>';
                                        }
                                    }else{
                                        $OPTION='<p class="text-danger">No hay roles disponibles</p>';
                                    }
                                    echo $OPTION;
                                ?>
                            </div>

                        </div><!-- END ROW -->
                        <input type="hidden" id="IDdatos" name="IDdatos">
                    </form>
                    <!-- End Contenido -->
                </div>
                <div class="modal-footer">

                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"><i class="bx bx-x d-block d-sm-none"></i><span class="d-none d-sm-block">Cancelar</span></button>
                    <span id="ButtonModal"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL DATOS -->

    <!-- MODAL CONFIRMAR -->
    <div class="modal fade text-left" id="ModalConfirmacion" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel4" data-bs-backdrop="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title text-white"><i class="bi bi-exclamation-diamond-fill" style="font-size: 2rem;"></i> Mensaje Confirmación</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i></button>
                </div>
                <div class="modal-body text-center">
                    <div id="MensajeConfirmar"></div>
                </div>
                <div class="modal-footer">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"><i class="bx bx-x d-block d-sm-none"></i><span class="d-none d-sm-block">Cancelar</span></button>
                    <span id="ButtonConfirmar"></span>
                </div>
            </div>
        </div>
    </div>



 <script src="assets/js/extensions/toastify/toastify.js"></script>
 <script src="assets/js/app.js"></script>   
 <script src="assets/js/jquery-2.1.1.js"></script>
 <script src="assets/js/extensions/jquery-validation/dist/jquery.validate.js"></script>
 <script src="assets/js/crud.js"></script>


</body>
</html>