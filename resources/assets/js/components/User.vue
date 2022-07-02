<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">               <!-- ICONO CREAR NUEVO REGISTRO -->
                    <big><mark><i class="fa fa-align-justify"></i> Usuarios</mark></big>
                    <button type="button" @click="abrirModal('persona','registrar')" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <template v-if="listado==1">            <!-- INICIO TABLA-LISTADO - listado=1 -->
                    <div class="card-body">
                        <table id="tabla-users" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Documento</th>
                                    <th>Primer Nombre</th>
                                    <th>Segundo Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Ciudad</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </template>                             <!-- FIN TABLA-LISTADO - listado=1 -->
                <template v-else-if="listado==0">       <!-- INICIO CREACION / MODIFICACION USUARIO - listado=0 -->
                    <div class="card-header" style="background-color:#20a8d8;">
                        <button type="button" class="close" @click="closeNewUser()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 style="color:white; background-color:#20a8d8; text-align: center;" v-text="tituloModal"></h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row border" :readonly="tipoAccion==2">
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Número Documento <span style="color:red;">(*)</span></label>
                                        <money type="moneda" class="form-control" v-model="num_documento" v-bind="money" :readonly="tipoAccion==2"></money>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Primer Nombre <span style="color:red;">(*)</span></label>
                                        <input type="text" :value="primernombre" @input="primernombre = $event.target.value.toUpperCase()" class="form-control" placeholder="Primer Nombre de la persona ...." :readonly="tipoAccion==2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Segundo Nombre <span style="color:red;">(*)</span></label>
                                        <input type="text" :value="segundonombre" @input="segundonombre = $event.target.value.toUpperCase()" class="form-control" placeholder="Segundo Nombre de la persona ...." :readonly="tipoAccion==2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Apellidos <span style="color:red;">(*)</span></label>
                                        <input type="text" :value="apellidos" @input="apellidos = $event.target.value.toUpperCase()" class="form-control" placeholder="Apellidos de la persona ...." :readonly="tipoAccion==2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Dirección </label>
                                        <input type="text" v-model="direccion" class="form-control" placeholder="Dirección ....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Teléfono </label>
                                        <input type="text" v-model="telefono" class="form-control" v-mask="'###-####-###'" placeholder="999-9999-999 ....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Ciudad </label>
                                        <input type="text" v-model="ciudad" class="form-control" placeholder="Ciudad ....">
                                </div>
                            </div>
                        </div>
                        <div class="container">                         <!-- BOTONES REGISTRAR - CERRAR  -->
                            <div class="registrar-Usuario">
                                <button type="button" style="margin: 5px" class="btn btn-secondary" @click="closeNewUser()">Cerrar</button>
                                <button type="button" style="margin: 5px" class="btn btn-primary" v-if="tipoAccion==1" @click="registrarPersona()">
                                    <i class="icon-plus"></i> Registrar Usuario
                                </button>
                                <button type="button" style="margin: 5px" class="btn btn-primary" v-if="tipoAccion==2" @click="actualizarPersona()">
                                    <i class="icon-plus"></i> Modificar Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                </template>                             <!-- FIN CREACION / MODIFICACIONI USUARIOS - listado=0  -->
            </div>
        </div>                                          <!-- FIN TABLA-LISTADO - listado=1 -->
    </main>
</template>

<script>
    import vSelect from 'vue-select';
    import {Money} from 'v-money';
    import VueMask from 'v-mask';
    Vue.use(VueMask);
    export default {
        components: {Money, vSelect},
        data (){
            return {
                money: {
                    decimal: '.',
                    thousands: ',',
                    prefix: ' ',
                    suffix: '',
                    precision: 0,
                    masked: false,
                    allowBlank: false,  //Para hacer referencia a esta propiedad, hay que hacerlo así--->   :alloBlank="true"
                },
                persona_id: 0,
                num_documento : '',
                primernombre : '',
                segundonombre : '',
                apellidos : '',
                direccion : '',
                telefono : '',
                ciudad : '',
                arrayUsuario : [],
                rowArrayUsuario : [],
                listado:1,
                tituloModal : '',
                tipoAccion : 0,
                nombreEmpresa : '',
                currentItem: '',
                userID : 0,
                msgError : '',
            }
        },

        methods : {
            listarUser(){
                let me=this;
                var url= '/user';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayUsuario = respuesta.personas;
                    me.userID =  respuesta.userid;
                    me.mytableUser();
                })
            },

			mytableUser(){
                let me=this;
                $(document).ready(function() {
                    var USTable = $('#tabla-users').DataTable({
                        stateSave: true,
                        pageLength : 	10,
                        scrollX    :  true,
                        order: [1, 'asc'],
                        data: me.arrayUsuario,
                        columnDefs: [
                            { className: 'dt-body-center', targets: [0,1] },
                            { orderable: false, targets: [0]},
                        ],                        
                        columns: [
                            { data: null,
                                render: function (data, type, row) {
                                    var tmpEstado = row['condicion'];
                                    var estadoIcon = '';
                                    if ( tmpEstado ){
                                        estadoIcon = 
                                        '<button type="button" class="button-editar-doc"   style="border: none;"><i class="fa fa-pen-alt      css_color_bluesea"></i></button>'+
                                        '<button type="button" class="button-eliminar-doc" style="border: none;"><i class="fa fa-trash        css_color_redpdf"></i></button>'
                                    } else {
                                        estadoIcon = 
                                        '<button type="button" class="button-editar-doc"   style="border: none;"><i class="fa fa-pen-alt      css_color_bluesea"></i></button>'+
                                        '<button type="button" class="button-eliminar-doc" style="border: none;"><i class="fa fa-trash        css_color_redpdf"></i></button>'
                                    }
                                    return estadoIcon;
                                },
                            },
                            { data: 'num_documento', render: $.fn.dataTable.render.number( '.', '.', 0 ) },
                            { data: 'primernombre'},
                            { data: 'segundonombre'},
                            { data: 'apellidos'},
                            { data: 'direccion'},
                            { data: 'telefono'},
                            { data: 'ciudad'},
                        ],
                        language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"},
                    });

	                $('#tabla-users tbody').on('click', '.button-editar-doc', function () {
                        var tr = $(this).closest('tr');
                        var row = USTable.row( tr ); 
                        var rowData = row.data();
                        me.rowArrayUsuario = rowData;

                        me.abrirModal('persona','actualizar');
                    });

	                $('#tabla-users tbody').on('click', '.button-eliminar-doc', function () {
                        var tr = $(this).closest('tr');
                        var row = USTable.row( tr ); 
                        var rowData = row.data();

                        if (rowData.user_id == me.userID){
                            swal({
                                type: 'error',
                                title: 'Error! No puedes eliminar tu propio Usuario',
                                text: 'Otro usuario Administrador debe hacerlo',
                            });

                            //alert( 'DANGER : : Has Clickeado : ' + row[0]+'\'s row ' + rowData.user_id + ' // ' + me.userID ); 
                            return;
                        }


                        swal({
                            title: 'Esta seguro de Eliminar este Usuario?',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar!',
                            cancelButtonText: 'Cancelar',
                            confirmButtonClass: 'btn btn-success',
                            cancelButtonClass: 'btn btn-danger',
                            buttonsStyling: false,
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {                
                                axios({
                                    method: 'delete',
                                    url:'/user/eliminar/' + rowData.id,
                                    headers: {'Content-Type': 'application/json'}
                                }).then(function (response) {
                                    USTable.row(row[0]).remove().draw();
                                    swal(
                                        'Eliminado!',
                                        'El Usuario ha sido Eliminado con Éxito.',
                                        'success'
                                    )
                                })
                            } else if (
                                    // Read more about handling dismissals
                                    result.dismiss === swal.DismissReason.cancel
                                    ) {
                                    // Factible eliminar junto con las dos llaves    
                                    }
                        });
                    });
                });                                
            },                                

            registrarPersona(){
                if (this.validarPersona()){
                    return;
                }

                let me = this;
                axios.post('/user/registrar',{
                    'num_documento' : this.num_documento,
                    'primernombre'  : this.primernombre,
                    'segundonombre' : this.segundonombre,
                    'apellidos'     : this.apellidos,
                    'direccion'     : this.direccion,
                    'telefono'      : this.telefono,
                    'ciudad'        : this.ciudad,

                }).then(function (response) {
                    me.listado=1;
                    me.cerrarModal();
                    me.listarUser();
                    swal({
                        type: 'success',
                        title: 'Salvado!',
                        text: 'El Nuevo Usuario se ha Registrado Correctamente'
                    })
                })
            },
            
            actualizarPersona(){
                if (this.validarPersona()){
                    return;
                }
                
                let me = this;
                axios.put('/user/actualizar',{
                    'id': this.persona_id,
                    'num_documento' : this.num_documento,
                    'primernombre': this.primernombre,
                    'segundonombre': this.segundonombre,
                    'apellidos': this.apellidos,
                    'direccion' : this.direccion,
                    'telefono' : this.telefono,
                    'ciudad' : this.ciudad,
                }).then(function (response) {
                    me.listado=1;
                    me.cerrarModal();
                    me.listarUser();
                    swal({
                        type: 'success',
                        title: 'Salvado!',
                        text: 'El Usuario se ha Actualizado Correctamente'
                    })
                })
            },
            validarPersona(){
                this.msgError = '';
                console.log('this EMAIL :: ', this.email);
                if (this.num_documento==0 && this.msgError=='') this.msgError = "Digite el Número de Documento.";
                if (!this.primernombre && this.msgError=='') this.msgError = "El Primer Nombre de la Persona no puede estar vacío.";
                if (!this.segundonombre && this.msgError=='') this.msgError = "El Segundo Nombre de la Persona no puede estar vacío.";
                if (!this.apellidos && this.msgError=='') this.msgError = "Los Apellidos de la Persona no puede estar vacío.";
                if (this.msgError.length) {
                    swal({
                        type: 'error',
                        title : this.msgError,
                    });
                    return 1;
                } else { return 0; }
            },

            closeNewUser(){
                this.cerrarModal();
                var tabla = $('#tabla-users').DataTable();
                tabla.search( '' );
                tabla.draw();                    
                tabla.destroy();
                this.listado=1;
                this.listarUser();
            },

            cerrarModal(){
                this.listado=1;
                this.tituloModal='';
                this.num_documento='';
                this.primernombre='';
                this.segundonombre='';
                this.apellidos='';
                this.direccion='';
                this.telefono='';
                this.ciudad='';
                this.idrol=0;
            },
            abrirModal(modelo, accion){
                this.listado=0;
                switch(modelo){
                    case "persona":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.tituloModal = 'Registrar Usuario';
                                this.num_documento='';
                                this.primernombre= '';
                                this.segundonombre= '';
                                this.apellidos= '';
                                this.direccion='';
                                this.telefono='';
                                this.ciudad='';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                this.tituloModal = 'Modificar Usuario';
                                this.persona_id     = this.rowArrayUsuario.id;
                                this.num_documento  = this.rowArrayUsuario.num_documento;
                                this.primernombre   = this.rowArrayUsuario.primernombre; 
                                this.segundonombre  = this.rowArrayUsuario.segundonombre;
                                this.apellidos      = this.rowArrayUsuario.apellidos;
                                this.direccion      = this.rowArrayUsuario.direccion;
                                this.telefono       = this.rowArrayUsuario.telefono;
                                this.ciudad         = this.rowArrayUsuario.ciudad;
                                this.tipoAccion=2;
                                break;
                            }
                        }
                    }
                }
            },

        },
        mounted() {
            this.listarUser();
        }
    }
</script>
<style>
    .swal2-popup #swal2-content {
        background-color: #FEFAE3;
        border: 1px solid #F0E1A1;
        text-align: center;
    }

    .button-eliminar-doc:hover{
        box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.40);
    }
    .button-editar-doc:hover{
        box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.40);
    }
    .css_color_redpdf {
        color: rgb(207, 0, 17);
    }
    .css_color_bluesea {
        color: rgb(30, 92, 112);
    }
    .css_color_greencheck{
        color: rgb(70, 160, 85);
    }

    input[type=moneda] {
        text-align:right;
    }
    .registrar-Usuario{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
</style>
