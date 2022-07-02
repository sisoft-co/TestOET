<template>
  <main class="main">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>
    <div class="container-fluid">
      <div class="card">
          <div class="card-header" style="background-color:#20a8d8;">
            <h4 style="color:white; background-color:#20a8d8;">Cambio de Contraseña</h4>
          </div>
          <div class="card-body">
            <form class="form-horizontal">
              <div class="form-group row">
                <label class="col-md-3">Por favor Digita tu Contraseña Actual</label>
                <div class="col-md-9">
                  <input class="form-control" :class="{'is-invalid': errors.current}" type="password" v-model="password.current" autocomplete="current-password">
                  <div class="invalid-feedback" v-if="errors.current">{{errors.current[0]}}</div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3">Nueva Contraseña</label>
                <div class="col-md-9">
                  <input class="form-control" :class="{'is-invalid': errors.password}" type="password" v-model="password.password" autocomplete="new-password">
                  <div class="invalid-feedback" v-if="errors.password">{{errors.password[0]}}</div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3">Confirme Nueva Contraseña</label>
                <div class="col-md-9">
                  <input class="form-control" :class="{'is-invalid': errors.password_confirmation}" type="password" v-model="password.password_confirmation" autocomplete="new-password">
                  <div class="invalid-feedback" v-if="errors.password_confirmation">{{errors.password_confirmation[0]}}</div>
                </div>
              </div>
            </form>
          </div>
          <div class="container">
            <div class="registrar-Password">
                <button type="button" style="margin: 5px" class="btn btn-primary" @click="updateAuthUserPassword()">
                    <i class="icon-plus"></i> Registrar Cambio de Contraseña
                </button>
            </div>
          </div>
      </div>
    </div>
  </main>
</template>


<script>
export default {
  data () {
    return {
      password: {},
      errors: {},
      submiting: false,
    }
  },

  methods: {
    confirmaReinicio() {
      axios.post('/logout')
      .then(response => {
        window.location = "/";
      })
    },

    updateAuthUserPassword () {
      this.submiting = true
      axios.put('/user/updateAuthUserPassword/', this.password)
      .then(response => {
        this.password = {}
        this.errors = {}
        this.submiting = false
        swal({
            type: 'success',
            title: 'Salvado!',
            text: 'La Aplicación se Reiniciará para Empezar una Nueva Sesión.',
            showConfirmButton: true,
            allowOutsideClick: false
        }).then(result => {
            if (result.value){
              this.confirmaReinicio();
            }
        })
      })
      .catch(error => {
        this.errors = error.response.data.errors
        this.submiting = false
      })
    }
  }
}

</script>
<style>
    .swal2-popup #swal2-content {
        background-color: #FEFAE3;
        border: 1px solid #F0E1A1;
        text-align: center;
    }

    .registrar-Password{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .invalid-feedback{
        font-weight: 700;
    }
</style>