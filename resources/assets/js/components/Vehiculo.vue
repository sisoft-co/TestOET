<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">               <!-- ICONO CREAR NUEVO REGISTRO -->
                    <big><mark><i class="fa fa-align-justify"></i> Vehiculos</mark></big>
                    <button type="button" @click="abrirModal('vehiculo','registrar')" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <template v-if="listado==1">            <!-- INICIO TABLA-LISTADO - listado=1 -->
                    <div class="card-body">
                        <table id="tabla-vehiculos" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Placa</th>
                                    <th>Color</th>
                                    <th>Marca</th>
                                    <th>Tipo Vehiculo</th>
                                    <th>Conductor</th>
                                    <th>Propietario</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </template>                             <!-- FIN TABLA-LISTADO - listado=1 -->
                <template v-else-if="listado==0">       <!-- INICIO CREACION / MODIFICACION VEHICULO - listado=0 -->
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
                                    <label>Placa <span style="color:red;">(*)</span></label>
                                    <input type="text" class="form-control" v-mask="'AAA###'" :value="placa" @input="placa = $event.target.value.toUpperCase()" placeholder="Placa....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Color <span style="color:red;">(*)</span></label>
                                        <input type="text" :value="color" @input="color = $event.target.value.toUpperCase()" class="form-control" placeholder="Color...." :readonly="tipoAccion==2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Marca <span style="color:red;">(*)</span></label>
                                        <input type="text" :value="marca" @input="marca = $event.target.value.toUpperCase()" class="form-control" placeholder="Marca...." :readonly="tipoAccion==2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo Vehículo <span style="color:red;">(*)</span></label>
                                        <select v-model="tipo" class="form-control" :disabled="tipoAccion==2">
                                            <option value="PARTICULAR">PARTICULAR</option>
                                            <option value="PUBLICO">PÚBLICO</option>
                                        </select>                                        
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Conductor <span style="color:red;">(*)</span></label>
                                    <v-select
                                        v-model = "nombreConductor"
                                        :reduce = "arrayConductor => arrayConductor.primernombre" 
                                        :options = "arrayConductor"
                                        label = "nombreConductor"
                                        @input = "getDatosConductor"
                                        placeholder = "Buscar Conductor..."
                                    >
                                    </v-select>
                                    <td align='center' v-text="currentItem.value"></td>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Propietario </label>
                                        <input type="text" v-model="direccion" class="form-control" placeholder="Dirección ....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label>Propietario </label>
                                        <input type="text" v-model="telefono" class="form-control" placeholder="Propietario ....">
                                </div>
                            </div>
                        </div>
                        <div class="container">                         <!-- BOTONES REGISTRAR - CERRAR  -->
                            <div class="registrar-Vehiculo">
                                <button type="button" style="margin: 5px" class="btn btn-secondary" @click="closeNewUser()">Cerrar</button>
                                <button type="button" style="margin: 5px" class="btn btn-primary" v-if="tipoAccion==1" @click="registrarPersona()">
                                    <i class="icon-plus"></i> Registrar Vehiculo
                                </button>
                                <button type="button" style="margin: 5px" class="btn btn-primary" v-if="tipoAccion==2" @click="actualizarPersona()">
                                    <i class="icon-plus"></i> Modificar Vehiculo
                                </button>
                            </div>
                        </div>
                    </div>
                </template>                             <!-- FIN CREACION / MODIFICACIONI - listado=0  -->
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
                nombreConductor : '',
                vehiculo_id : 0,
                placa : '',
                color : '',
                marca : '',
                tipo : '',
                conductor : 0,
                propietario : 0,

                nombrePersona : '',
                conductor_id: 0,
                num_documento : '',
                primernombre : '',
                segundonombre : '',
                apellidos : '',
                direccion : '',
                telefono : '',
                ciudad : '',
                arrayVehiculo : [],
                rowArrayVehiculo : [],
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
            selectConductor(){
               let me=this;
               var url= '/vehiculo/selectPersonas';
               axios.get(url).then(function (response) {
                  let respuesta = response.data;
                  me.arrayConductor=respuesta.personas;
                  console.log('AQUI VA', me.arrayConductor)
               })
            },
            getDatosConductor(val1){
               let me = this;
               console.log('Veamos si pasa', val1)
               if (val1 !== null) {
                  me.conductor = val1.id;
               }
            },

            listarVehiculo(){
               let me=this;
               var url= '/vehiculo';
               axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.userID =  respuesta.userid;
                    me.arrayVehiculo = respuesta.vehiculos;
                    me.mytableVehiculo();
               })
            },

            mytableVehiculo(){
               let me=this;
               $(document).ready(function() {
                  var VHTable = $('#tabla-vehiculos').DataTable({
                        stateSave: true,
                        pageLength : 	10,
                        scrollX    :  true,
                        order: [1, 'asc'],
                        data: me.arrayVehiculo,
                        columnDefs: [
                           { className: 'dt-body-center', targets: [0,1] },
                           { orderable: false, targets: [0]},
                        ],                        
                        columns: [
                           { data: null,
                              render: function (data, type, row) {
                                        var tmpEstado = 0;
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
                           { data: 'placa'},
                           { data: 'color'},
                           { data: 'marca'},
                           { data: 'tipo'},
                           { data: 'conductor'},
                           { data: 'propietario'},
                        ],
                        language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"},
                  });

                  $('#tabla-vehiculos tbody').on('click', '.button-editar-doc', function () {
                        var tr = $(this).closest('tr');
                        var row = VHTable.row( tr ); 
                        var rowData = row.data();
                        me.rowArrayVehiculo = rowData;

                        me.abrirModal('vehiculo','actualizar');
                  });

                  $('#tabla-vehiculos tbody').on('click', '.button-eliminar-doc', function () {
                        var tr = $(this).closest('tr');
                        var row = VHTable.row( tr ); 
                        var rowData = row.data();

                        swal({
                           title: 'Esta seguro de Eliminar este Vehiculo?',
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
                                    url:'/vehiculo/eliminar/' + rowData.id,
                                    headers: {'Content-Type': 'application/json'}
                              }).then(function (response) {
                                    VHTable.row(row[0]).remove().draw();
                                    swal(
                                       'Eliminado!',
                                       'El Vehiculo ha sido Eliminado con Éxito.',
                                       'success'
                                    )
                              })
                           } else if (
                                    // Read more about handling dismissals
                                    result.dismiss === swal.DismissReason.cancel
                                    ) {
                                    // More commands  
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
                        text: 'El Nuevo Vehiculo se ha Registrado Correctamente'
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
                        text: 'El Vehiculo se ha Actualizado Correctamente'
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
                  case "vehiculo":
                  {
                        switch(accion){
                           case 'registrar':
                           {
                              this.tituloModal = 'Registrar Vehiculo';
                              this.placa='';
                              this.color= '';
                              this.marca= '';
                              this.tipo= '';
                              this.conductor='';
                              this.propietario='';
                              this.tipoAccion = 1;
                              break;
                           }
                           case 'actualizar':
                           {
                              this.tituloModal = 'Modificar Vehiculo';
                              this.vehiculo_id = this.rowArrayVehiculo.id;
                              this.placa       = this.rowArrayVehiculo.placa;
                              this.color       = this.rowArrayVehiculo.color;
                              this.marca       = this.rowArrayVehiculo.marca;
                              this.tipo        = this.rowArrayVehiculo.tipo;
                              this.conductor   = this.rowArrayVehiculo.conductor;
                              this.propietario = this.rowArrayVehiculo.propietario;
                              this.tipoAccion=2;
                              break;
                           }
                        }
                  }
               }
            },
         },
         
        mounted() {
            this.listarVehiculo();
            this.selectConductor();

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
    .registrar-Vehiculo{
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
