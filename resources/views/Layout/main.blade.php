<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta20
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('assets/image/Asset 1.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('assets/image/Asset 1.png') }}" type="image/x-icon" />
    <!-- CSS files -->
    <link href="{{ asset('assets/vendor/sweetalert/sweetalert2.min.css') }}?{{ rand() }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/toastify/toastify.css') }}?{{ rand() }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/freezeui/freeze-ui.min.css') }}?{{ rand() }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/datatable/datatables.min.css') }}?{{ rand() }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/tabler/css/tabler.min.css') }}?{{ rand() }}" rel="stylesheet" />
    @vite(['resources/js/app.js'])
    @stack('mystyles')
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    <script>
        const baseUrl = '{{ url('') }}'
    </script>
    @livewireStyles
    @vite('resources/js/app.js')
</head>

<body class=" layout-fluid">
    <div class="page">
        <!-- Navbar -->
        @include('Layout.partials.topbar')
        <div class="page-wrapper">
            <!-- Page header -->
            @yield('content')
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    Copyright © 2023
                                    <a href="." class="link-secondary">Tabler</a>.
                                    All rights reserved.
                                </li>
                                <li class="list-inline-item">
                                    <a href="./changelog.html" class="link-secondary" rel="noopener">
                                        v1.0.0-beta20
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/tabler/libs/bootstrap/dist/js/popper.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/tabler/libs/bootstrap/dist/js/bootstrap.bundle.min.js') . '?' . rand() }}">
    </script>
    <script src="{{ asset('assets/vendor/tabler/libs/bootstrap/dist/js/bootstrap.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/fontawesome/js/all.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert/sweetalert2.all.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/toastify/toastify.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/freezeui/freeze-ui.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatables.min.js') . '?' . rand() }}"></script>
    <script src="{{ asset('assets/vendor/tabler/js/tabler.min.js') . '?' . rand() }}"></script>
    <script>
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success mx-2',
                cancelButton: 'btn btn-danger mx-2'
            },
            buttonsStyling: false
        })

        const blockUI = () => {
            FreezeUI({
                text: 'Processing'
            });
        }

        const unBlockUI = () => {
            UnFreezeUI();
        }

        const onSaveJson = async (url, data, method) => {
            return await fetch(url, {
                method: method,
                body: data,
                headers: {
                    "Content-Type": "application/json",
                },
            }).then(response => {
                if (!response.ok) {
                    unBlockUI()
                    throw new Error(swalWithBootstrapButtons.fire('Error',
                        "Terjadi kesalahan saat menyimpan data", 'error'))
                }

                return response.json()
            }).then(response => {
                return response
            });
        }

        const onSaveForm = async (url, data, method, csrf) => {
            return await fetch(url, {
                method: method,
                body: data,
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            }).then(response => {
                if (!response.ok) {
                    unBlockUI()
                    throw new Error(swalWithBootstrapButtons.fire('Error',
                        "Terjadi kesalahan saat menyimpan data", 'error'))
                }

                return response.json()
            }).then(response => {
                return response
            });
        }

        const createDropdown = async (url, element, placeholder, selected) => {
            element.clearStore();
            element.clearChoices();

            if (placeholder != "") {
                element.setChoices([{
                    label: placeholder,
                    value: "",
                }, ]);
            }

            if (typeof url == "object") {
                element.setChoices(url);
            } else {
                await fetch(url)
                    .then((response) => {
                        if (!response.ok) {
                            unBlockUI()
                            throw new Error(
                                swalWithBootstrapButtons.fire(
                                    "Error",
                                    "Terjadi kesalahan saat mengambil data",
                                    "error"
                                )
                            );
                        }

                        return response.json();
                    })
                    .then((response) => {
                        unBlockUI()
                        element.setChoices(response);
                    });
            }

            element.setChoiceByValue(selected);
        }

        const loggedOut = async (csrf) => {
            await Swal.fire({
                title: 'Log Out',
                text: "Apakah yakin akan logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then(async (result) => {
                if (result.value) {
                    const url = `${baseUrl}/logout`;

                    const fetchOptions = {
                        method: "POST",
                        body: JSON.stringify({
                            _token: csrf
                        }),
                        headers: {
                            "Content-Type": "application/json",
                        },
                    };

                    await fetch(url, fetchOptions)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(swalWithBootstrapButtons.fire('Terjadi kesalahan',
                                    'Saat mengirim data', 'error'));
                            }

                            return response.json()
                        }).then(response => {
                            location.replace(`${baseUrl}/login`);
                        });
                }
            });
        }

        window.addEventListener('swal-modal', event => {
            swal.fire(event.detail[0].message, event.detail[0].text, event.detail[0].type)
        });
    </script>
    <script></script>
    @livewireScripts
    @stack('myscript')
</body>

</html>
