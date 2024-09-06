let validator = null
$(document).ready(function(){
    load(1);

    $.validator.addMethod("soloLetras", function(value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿ\s]+$/.test(value);
    }, "Solo se permiten letras, espacios y tildes.");

    $.validator.addMethod("checkRoles", function(value, element) {
        return $('input[name="roles[]"]:checked').length > 0;
    }, "Por favor, selecciona al menos un rol.");

    validator = $('#FormDatos').validate({
        rules: {
            nombre: {
                required: true,
                minlength: 10,
                soloLetras: true 
            },
            correo: {
                required: true,
                email: true
            },
            sexo: {
                required: true
            },
            area: {
                required: true
            },
            descripcion: {
                required: true,
                minlength: 10
            },
            'roles[]': {
                checkRoles: true
            }
        },
        messages: {
            nombre: {
                required: "Por favor, ingresa tu nombre completo",
                minlength: "El nombre debe tener al menos 10 caracteres"
            },
            correo: {
                required: "Por favor, ingresa tu correo electrónico",
                email: "Por favor, ingresa un correo válido"
            },
            sexo: {
                required: "Por favor, selecciona tu sexo"
            },
            area: {
                required: "Por favor, selecciona un área"
            },
            descripcion: {
                required: "Por favor, ingresa una descripción",
                minlength: "La descripción debe tener al menos 10 caracteres"
            },
            'roles[]': {
                checkRoles: "Por favor, selecciona al menos un rol"
            }
        },
        errorPlacement: function(error, element) {
            if (element.is(":radio") || element.is(":checkbox")) {
                error.appendTo(element.parents('.form-group'));
            } else {
                error.insertAfter(element);
            }
        }
    });


});



// FUNCION AGREGAR EMPLEADO
const add=()=>{

    $("#TitleModal").html('Agregar Empleado <i class="fas fa-user-plus"></i>');
    $("#FormDatos")[0].reset();
    $("#ButtonModal").html('<button type="button" class="btn btn-success ml-1" onClick="Agregar();"><i class="far fa-check-circle"></i> Guardar</button>');
    $("#ModalDatos").modal('show');
    validator.resetForm(); 
}

// FUNCION GUARDAR DATOS DE EMPLEADO
const Agregar=()=>{
    let sexo=$('input:radio[name=sexo]:checked').val();
    let boletin= $('#boletin').is(':checked') ? 1 : 0;

    // Verificar si el formulario es válido
    if (!$('#FormDatos').valid()) {
        notify.alert('danger','top','center','Formulario no válido, por favor corrige los errores.');
        return false;
    }

	let roles=[];
	$('input:checkbox[name="roles[]"]:checked').each(function(){
		roles.push($(this).val());
	});


    let dato=$("#FormDatos").serialize();

    $.ajax({
        type:"POST",
        url:"ajax/procesos.php",
        data:dato+"&action=AddEmpleado&sexo="+sexo+"&roles="+roles+"&boletin="+boletin,
        beforeSend: function(objeto){
            $(".load").show();
            $(".spinner-border").show();
            $(".btn").attr('disabled',true);
        },
        success: function(data){

            $(data).each(function(){
                if(data.estado==1){
                    if(data.datos.Validacion=='OK'){ 
                        notify.alert('success','top','center','Empleado agregado con exito.');
                        $("#ModalDatos").modal('hide');
                        load(1);
                    }else{
                        notify.alert('danger','top','right',data.datos.Mensaje);
                    }

                }else{
                    notify.alert('danger','top','right',data.mensaje);
                }

                $(".load").hide();
                $(".spinner-border").hide();
                $(".btn").attr('disabled',false);

            }); 
        },error:function(jqXHR, exception){

            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Verificar su estado de internet';
            } else if (jqXHR.status == 404) {
                msg = 'Página solicitada no encontrada. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Error interno del servidor [500].';
            } else if (exception === 'parsererror') {
                msg = 'Se solicitó el error de JSON.';
            } else if (exception === 'timeout') {
                msg = 'Error de tiempo de espera.';
            } else if (exception === 'abort') {
                msg = 'Ajax petición abortada.';
            } else {
                msg = 'Error no detectado.\n' + jqXHR.responseText;
            }
            $(".load").fadeOut(1000);
            $(".spinner-border").hide();
            $(".btn").attr('disabled',false);
            notify.alert('danger','top','right','Ocurrió un error inesperado, inténtelo mas tarde. '+msg); 
        }
    });
}

// LISTADO DE EMPLEADOS
const load=(page)=>{   

    let buscar=$("#Buscar").val();
     
    $(".load").fadeIn('slow');

    $.ajax({
        type:'POST',
        url:'ajax/procesos.php',
        data:{action:'ListadoEmpleados', page:page, buscar:buscar},
        beforeSend:function(){
            $(".load").show();
        },
        success:function(data){
            $(data).each(function(){
            if(data.estado==1){
                if(data.datos.Validacion=='OK'){

                    if(data.datos.Cantidad>0){

                        let valores=eval(data.datos.Row);
                        let Tabla=``;
                        let C=0;

                        valores.forEach(element => {
                            C++;
                            let sexo = element.sexo=='M' ? 'Masculino' : 'Femenino';
                            let boletin = element.boletin=='1' ? 'Sí' : 'No';

                            let Button = `<button type="button" class="btn btn-warning btn-sm ml-1" onClick="Editar(${element.id})" title="Modificar"><i class="bi bi-pen"></i></button> `;
                            Button+= `<button type="button" class="btn btn-danger btn-sm ml-1" onClick="Eliminar(${element.id})" title="Eliminar"><i class="bi bi-trash"></i></button>`;

                            Tabla+=`<tr>
                                <td class="text-center">${C}</td>
                                <td class="text-start">${element.nombre}</td>
                                <td class="text-center">${element.email}</td>
                                <td class="text-center">${sexo}</td>
                                <td class="text-center">${element.area}</td>
                                <td class="text-center">${boletin}</td>
                                <td class="text-center">${Button}</td>
                            </tr>`;
                        });//End For

            
                        $("#DatosTable tbody").html(Tabla+data.datos.Navegacion);

                    }else{
                        $("#DatosTable tbody").html(`<tr><td colspan="7" align="center"><h4>No hay registros <i class="far fa-grin-beam-sweat"></i></h4></td></tr>`);
                    }

                }else{
                    notify.alert('danger','top','right',data.datos.Mensaje);
                }

            }else{
                notify.alert('danger','top','right',data.mensaje);
            }
            $(".load").fadeOut(1000);
            }); 

        },

        error:function(jqXHR, exception){

            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Verificar su estado de internet';
            } else if (jqXHR.status == 404) {
                msg = 'Página solicitada no encontrada. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Error interno del servidor [500].';
            } else if (exception === 'parsererror') {
                msg = 'Se solicitó el error de JSON.';
            } else if (exception === 'timeout') {
                msg = 'Error de tiempo de espera.';
            } else if (exception === 'abort') {
                msg = 'Ajax petición abortada.';
            } else {
                msg = 'Error no detectado.\n' + jqXHR.responseText;
            }
            $(".load").fadeOut(1000);
            notify.alert('danger','top','right','Ocurrió un error inesperado, inténtelo mas tarde. '+msg); 
        }
    });

}

// EDITAR EMPLEADO
const Editar=(id)=>{

    $("#TitleModal").html('Editar Empleado <i class="fas fa-user-edit"></i>');
    $("#ButtonModal").html('<button type="button" class="btn btn-success ml-1" onClick="Update()"><i class="fas fa-sync"></i> Actualizar</button>');
    $("#FormDatos")[0].reset();
    $("#IDdatos").val(id);
    validator.resetForm(); 

    $.ajax({
        type:"POST",
        url:"ajax/procesos.php",
        data:{action:'InformacionEmpleado', id:id},
        beforeSend: function(){
            $(".load").show();
            $(".btn").attr('disabled',true);
        },
        success: function(data){

            $(data).each(function(){
                if(data.estado==1){
                    if(data.datos.Validacion=='OK'){ 

                        let valores=eval(data.datos.row);
                        let roles=eval(data.datos.roles);

                        $("#nombre").val(valores.nombre);
                        $("#correo").val(valores.email);
                        $(`input[name="sexo"][value="${valores.sexo}"]`).prop('checked', true);
                        $("#area").val(valores.area);
                        $("#descripcion").val(valores.descripcion);

                        let boletin = valores.boletin=='1' ? true : false;
                        $('input:checkbox[name=boletin]').prop('checked',boletin);

                        // Listado de roles asignados
                        roles.forEach(element => {
                            $(`input:checkbox[name="roles[]"][value="${element.id}"]`).prop('checked', true);
                        });
                        
                        $("#ModalDatos").modal('show');

                    }else{
                        notify.alert('danger','top','right',data.datos.Mensaje);
                    }

                }else{
                    notify.alert('danger','top','right',data.mensaje);
                }

                $(".load").hide();
                $(".btn").attr('disabled',false);

            }); 
        },error:function(jqXHR, exception){

            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Verificar su estado de internet';
            } else if (jqXHR.status == 404) {
                msg = 'Página solicitada no encontrada. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Error interno del servidor [500].';
            } else if (exception === 'parsererror') {
                msg = 'Se solicitó el error de JSON.';
            } else if (exception === 'timeout') {
                msg = 'Error de tiempo de espera.';
            } else if (exception === 'abort') {
                msg = 'Ajax petición abortada.';
            } else {
                msg = 'Error no detectado.\n' + jqXHR.responseText;
            }
            $(".load").fadeOut(1000);
            $(".btn").attr('disabled',false);
            notify.alert('danger','top','right','Ocurrió un error inesperado, inténtelo mas tarde. '+msg); 
        }
    });
}

// ACTUALIZAR INFORMACION EMPLEADO
const Update=()=>{

    let sexo=$('input:radio[name=sexo]:checked').val();
    let boletin= $('#boletin').is(':checked') ? 1 : 0;

    // Verificar si el formulario es válido
    if (!$('#FormDatos').valid()) {
        notify.alert('danger','top','center','Formulario no válido, por favor corrige los errores.');
        return false;
    }

	let roles=[];
	$('input:checkbox[name="roles[]"]:checked').each(function(){
		roles.push($(this).val());
	});

    let dato=$("#FormDatos").serialize();

    $.ajax({
        type:"POST",
        url:"ajax/procesos.php",
        data:dato+"&action=UpdateEmpleado&sexo="+sexo+"&boletin="+boletin+"&roles="+roles,
        beforeSend: function(objeto){
            $(".load").show();
            $(".spinner-border").show();
            $(".btn").attr('disabled',true);
        },
        success: function(data){

            $(data).each(function(){
                if(data.estado==1){
                    if(data.datos.Validacion=='OK'){ 
                        notify.alert('success','top','center','Empleado actualizado con exito.');
                        $("#ModalDatos").modal('hide');
                        load(1);
                    }else{
                        notify.alert('danger','top','right',data.datos.Mensaje);
                    }

                }else{
                    notify.alert('danger','top','right',data.mensaje);
                }

                $(".load").hide();
                $(".spinner-border").hide();
                $(".btn").attr('disabled',false);

            }); 
        },error:function(jqXHR, exception){

            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Verificar su estado de internet';
            } else if (jqXHR.status == 404) {
                msg = 'Página solicitada no encontrada. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Error interno del servidor [500].';
            } else if (exception === 'parsererror') {
                msg = 'Se solicitó el error de JSON.';
            } else if (exception === 'timeout') {
                msg = 'Error de tiempo de espera.';
            } else if (exception === 'abort') {
                msg = 'Ajax petición abortada.';
            } else {
                msg = 'Error no detectado.\n' + jqXHR.responseText;
            }
            $(".load").fadeOut(1000);
            $(".spinner-border").hide();
            $(".btn").attr('disabled',false);
            notify.alert('danger','top','right','Ocurrió un error inesperado, inténtelo mas tarde. '+msg); 
        }
    });
}

// ELMINAR EMPLEADO
const Eliminar=(id)=>{
    $("#MensajeConfirmar").html('<h5>¿Deseas continuar con la eliminación de este empleado?</h5> <p>Recuerde que no se podrá revertir este proceso.</p>');
    $("#ButtonConfirmar").html(`<button type="button" class="btn btn-success ml-1" onClick="YesEliminar(${id})"><i class="far fa-check-circle"></i> OK, Continuar</button>`);
    $("#ModalConfirmacion").modal('show');
}

// CONFIRMAR LA ELIMINACION
const YesEliminar=(id)=>{
  
     $(".load").fadeIn('slow');

    $.ajax({
        type:'POST',
        url:'ajax/procesos.php',
        data:{action:'DeleteEmpleado', id:id},
        beforeSend:function(){
            $(".load").show();
            $(".spinner-border").show();
            $(".btn").attr('disabled',true);
        },
        success:function(data){
            $(data).each(function(){
            if(data.estado==1){
                if(data.datos.Validacion=='OK'){ 
                    notify.alert('success','top','center','Empleado Eliminado con éxito.');
                    $("#ModalConfirmacion").modal('hide');
                    load(1);
                }else{
                    notify.alert('danger','top','right',data.datos.Mensaje);
                }

            }else{
                notify.alert('danger','top','right',data.mensaje);
            }
            $(".load").fadeOut(1000);
            $(".btn").attr('disabled',false);
            $(".spinner-border").fadeOut(1000);
            }); 

        },

        error:function(jqXHR, exception){

            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Verificar su estado de internet';
            } else if (jqXHR.status == 404) {
                msg = 'Página solicitada no encontrada. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Error interno del servidor [500].';
            } else if (exception === 'parsererror') {
                msg = 'Se solicitó el error de JSON.';
            } else if (exception === 'timeout') {
                msg = 'Error de tiempo de espera.';
            } else if (exception === 'abort') {
                msg = 'Ajax petición abortada.';
            } else {
                msg = 'Error no detectado.\n' + jqXHR.responseText;
            }
            $(".load").fadeOut(1000);
            $(".btn").attr('disabled',false);
            $(".spinner-border").fadeOut(1000);
            notify.alert('danger','top','right','Ocurrió un error inesperado, inténtelo mas tarde. '+msg); 
        }
    });
}