<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
        with font-awesome or any other icon font library -->

    <li class="nav-header">Navigation</li>
    <li class="nav-item">
        <a href="{{route('suppliers')}}" class="nav-link">
            <i class="fas fa-list"></i>
            <p>
                Suppliers
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('invoices')}}" class="nav-link">
            <i class="fas fa-list"></i>
            <p>
                Invoices
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('invoice-batches')}}" class="nav-link">
            <i class="fas fa-list"></i>
            <p>
                Invoice Batches
            </p>
        </a>
    </li>
</ul>