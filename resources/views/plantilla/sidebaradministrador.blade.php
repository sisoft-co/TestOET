<div class="sidebar bg-primary">
    <nav class="sidebar-nav">
        <ul class="nav nav-pills">
            <li class="nav-title">
                Opciones de Menú
            </li>
            <!-- ÍTMES de Menú Lateral  -->
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-layers"></i> Catálogos</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=1" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-people"></i> Personas</a>
                    </li>
                    <li @click="menu=2" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-truck"></i> Vehiculos</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-book-open"></i> Consultas</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=3" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-book-open"></i> Listado Vehículos</a>
                    </li>
                </ul>
            </li>

            <li @click="menu=10" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-info"></i> Acerca de ...<span class="badge badge-info"></span></a>
            </li>
            <!-- ÍTEMS de Menú Lateral -- FIN  -->
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>