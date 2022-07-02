<template>
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">A.C.M.E</a></li>
        <p class="Title">{{nombreEmpresa}}</p>
    </ol>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                
            </div>
            <div class="car-body">
                <div class="row">
                </div>
            </div>
        </div>
    </div>

</main>
</template>
<script>
    export default {
        data (){
            return {
                nombreEmpresa : '',
            }
        },
        methods : {
        },
        mounted() {
        }
    }

    $(document).ready(function() {
        $(function () {
            $(".sidebar .nav-link").click(function () {
                $("ul.nav a.nav-link:not(.active)").css("background-color", "#20a8d8");
                $("ul.nav a.nav-link:hover, ul.nav a.nav-link:focus, ul.nav a.nav-link:active, .dropdown-toggle i ").css("background-color", "#25b9ec");
                $("a.nav-link.dropdown-toggle ").css("background-color", "white");
            })
        })        
    })
</script>

<style>
    .sidebar .nav-dropdown-items .nav-item {
        padding-left: 15px;
        background-color: #20a8d8;
    }
    .sidebar .nav-dropdown.open .nav-link{
         -webkit-text-stroke: thin;
    }
    .sidebar .sidebar-nav::-webkit-scrollbar-thumb {
        height: 50px;
        background-color: #c1c1c1;
        background-clip: content-box;
        border-color: transparent;
        border-style: solid;
        border-width: 1px 0px;
    }
    .sidebar .sidebar-nav::-webkit-scrollbar-track {
        background-color: #f1f1f1;
        border-right: 0; /*1px solid #1f292e; */
        border-left: 0; /*1px solid #1f292e; */
    }
</style>