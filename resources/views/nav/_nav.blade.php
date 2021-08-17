<ul id="nav" class="nav nav-pills nav-sidebar flex-column" v-cloak ref="nav" data-widget="treeview" role="menu"
    data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
        with font-awesome or any other icon font library -->
    <li class="nav-header">Navigation</li>
    @if(auth()->user()->getActiveCompany()->isXeroConnected())
    <li class="nav-item">
        <a href="{{route('suppliers')}}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
                Suppliers
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('invoices')}}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
                Invoices
            </p>
            <span v-if="invoiceCount > 0" class="badge badge-danger navbar-badge font-weight-bold"
                style="font-size:12px;">
                @{{invoiceCount}}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('credit-notes', ['status' => 'AUTHORISED'])}}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
                Credit Notes
            </p>
            <span v-if="creditNoteCount > 0" class="badge badge-danger navbar-badge font-weight-bold"
                style="font-size:12px;">
                @{{creditNoteCount}}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('invoice-batches', ['status' => 'Not Yet Generated'])}}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
                Invoice Batches
            </p>
            <span v-if="invoiceBatchCount > 0" class="badge badge-danger navbar-badge font-weight-bold"
                style="font-size:12px;">
                @{{invoiceBatchCount}}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('invoice-histories')}}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
                Invoice History
            </p>
        </a>
    </li>
    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a href="{{route('companies')}}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
                Companies
            </p>
        </a>
    </li>
    @endif

    @if(auth()->user()->isAdmin())
    <li class="nav-item">
        <a href="{{route('users')}}" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
                Users
            </p>
        </a>
    </li>
    @endif
    <li class="nav-item">
        <a href="{{route('xero_status')}}" class="nav-link">
            <i class="nav-icon fas fa-link"></i>
            <p>
                Xero Connection
            </p>
        </a>
    </li>
    {{-- <li class="nav-item">
        <a href="{{route('currencies')}}" class="nav-link">
    <i class="nav-icon fas fa-money-bill-wave"></i>
    <p>
        Currencies
    </p>
    </a>
    </li> --}}
    @endif

    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
                My Companies
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @foreach (auth()->user()->userCompanies as $userCompany)
            <li class="nav-item">
                <a href="{{route('set_active_company', $userCompany->id)}}" class="nav-link">
                    @if($userCompany->is_active)
                    <i class="far fa-dot-circle nav-icon"></i>
                    @else
                    <i class="far fa-circle nav-icon"></i>
                    @endif
                    <p>{{$userCompany->company->name}}</p>
                </a>
            </li>
            @endforeach
        </ul>
    </li>
</ul>
@push('scripts')
<script src="{{ mix('js/nav.js') }}"></script>
@endpush