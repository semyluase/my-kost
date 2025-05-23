@php
    use App\Models\RoleMenu;

@endphp
<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl" bis_skin_checked="1">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav flex-row order-md-last" bis_skin_checked="1">
            <div class="d-none d-md-flex" bis_skin_checked="1">
                <div class="nav-item dropdown d-none d-md-flex me-3" bis_skin_checked="1">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Show notifications">
                        <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path
                                d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                            </path>
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                        </svg>
                        <span class="badge bg-red"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"
                        bis_skin_checked="1">
                        <div class="card" bis_skin_checked="1">
                            <div class="card-header" bis_skin_checked="1">
                                <h3 class="card-title">Last updates</h3>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable" bis_skin_checked="1">
                                <div class="list-group-item" bis_skin_checked="1">
                                    <div class="row align-items-center" bis_skin_checked="1">
                                        <div class="col-auto" bis_skin_checked="1"><span
                                                class="status-dot status-dot-animated bg-red d-block"></span></div>
                                        <div class="col text-truncate" bis_skin_checked="1">
                                            <a href="#" class="text-body d-block">Example 1</a>
                                            <div class="d-block text-secondary text-truncate mt-n1"
                                                bis_skin_checked="1">
                                                Change deprecated html tags to text decoration classes (#29604)
                                            </div>
                                        </div>
                                        <div class="col-auto" bis_skin_checked="1">
                                            <a href="#" class="list-group-item-actions">
                                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item" bis_skin_checked="1">
                                    <div class="row align-items-center" bis_skin_checked="1">
                                        <div class="col-auto" bis_skin_checked="1"><span
                                                class="status-dot d-block"></span></div>
                                        <div class="col text-truncate" bis_skin_checked="1">
                                            <a href="#" class="text-body d-block">Example 2</a>
                                            <div class="d-block text-secondary text-truncate mt-n1"
                                                bis_skin_checked="1">
                                                justify-content:between ⇒ justify-content:space-between (#29734)
                                            </div>
                                        </div>
                                        <div class="col-auto" bis_skin_checked="1">
                                            <a href="#" class="list-group-item-actions show">
                                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item" bis_skin_checked="1">
                                    <div class="row align-items-center" bis_skin_checked="1">
                                        <div class="col-auto" bis_skin_checked="1"><span
                                                class="status-dot d-block"></span></div>
                                        <div class="col text-truncate" bis_skin_checked="1">
                                            <a href="#" class="text-body d-block">Example 3</a>
                                            <div class="d-block text-secondary text-truncate mt-n1"
                                                bis_skin_checked="1">
                                                Update change-version.js (#29736)
                                            </div>
                                        </div>
                                        <div class="col-auto" bis_skin_checked="1">
                                            <a href="#" class="list-group-item-actions">
                                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted"
                                                    width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item" bis_skin_checked="1">
                                    <div class="row align-items-center" bis_skin_checked="1">
                                        <div class="col-auto" bis_skin_checked="1"><span
                                                class="status-dot status-dot-animated bg-green d-block"></span></div>
                                        <div class="col text-truncate" bis_skin_checked="1">
                                            <a href="#" class="text-body d-block">Example 4</a>
                                            <div class="d-block text-secondary text-truncate mt-n1"
                                                bis_skin_checked="1">
                                                Regenerate package-lock.json (#29730)
                                            </div>
                                        </div>
                                        <div class="col-auto" bis_skin_checked="1">
                                            <a href="#" class="list-group-item-actions">
                                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted"
                                                    width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown" bis_skin_checked="1">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm"
                        style="background-image: url({{ asset('assets/vendor/tabler/img/avatars/hacker.png') }})"></span>
                    <div class="d-none d-xl-block ps-2" bis_skin_checked="1">
                        <div bis_skin_checked="1">{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-secondary" bis_skin_checked="1">{{ auth()->user()->role->name }}
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" bis_skin_checked="1">
                    <a href="#" class="dropdown-item">Status</a>
                    <a href="{{ url('/settings/users/profiles') }}" class="dropdown-item">Profile</a>
                    <a href="#" class="dropdown-item">Feedback</a>
                    <div class="dropdown-divider" bis_skin_checked="1"></div>
                    <a href="./settings.html" class="dropdown-item">Settings</a>
                    <a href="javascript:;" class="dropdown-item"
                        onclick="loggedOut('{{ csrf_token() }}')">Logout</a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu" bis_skin_checked="1">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center"
                bis_skin_checked="1">
                <ul class="navbar-nav">
                    {!! RoleMenu::createMenu() !!}
                </ul>
            </div>
        </div>
    </div>
</header>
