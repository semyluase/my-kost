const nameUser = document.querySelector("#name");
const idUser = document.querySelector("#id-user");
const usernameUser = document.querySelector("#username");
const passwordUser = document.querySelector("#password");
const passwordLabelUser = document.querySelector("#password-label");

let url, data, method;

const fnUser = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add"),
            btnSave: document.querySelector("#btn-save"),
        },
        dropdowns: {
            roleDropdown: new Choices(document.querySelector("#role")),
            locationDropdown: new Choices(document.querySelector("#location")),
        },
        modals: {
            userModal: new bootstrap.Modal(
                document.querySelector("#modal-user")
            ),
        },
        tables: {
            tbUser: $("#tb-user").DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ordering: false,
                ajax: {
                    url: `${baseUrl}/settings/users/get-all-data`,
                },
            }),
        },
    },

    onEdit: async (id) => {
        await fetch(`${baseUrl}/settings/users/${id}/edit`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        swalWithBootstrapButtons.fire(
                            "Failed",
                            "Something error while get data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then(async (response) => {
                nameUser.value = response.name;
                idUser.value = response.id;
                usernameUser.value = response.username;

                if (!passwordUser.classList.contains("d-none")) {
                    passwordUser.classList.add("d-none");
                }

                if (!passwordLabelUser.classList.contains("d-none")) {
                    passwordLabelUser.classList.add("d-none");
                }

                if (!usernameUser.readonly) {
                    usernameUser.setAttribute("readonly", true);
                    usernameUser.classList.add(["bg-gray-500"]);
                }

                await createDropdown(
                    `${baseUrl}/utils/dropdowns/get-homes`,
                    fnUser.init.dropdowns.locationDropdown,
                    "Pilih Lokasi Kos",
                    response.home_id
                );

                await createDropdown(
                    `${baseUrl}/utils/dropdowns/get-roles`,
                    fnUser.init.dropdowns.roleDropdown,
                    "Pilih Role",
                    response.role_id
                );

                fnUser.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnUser.init.modals.userModal.show();
            });
    },

    onDelete: async (id, csrf) => {
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You want to delete this data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            })
            .then(async (result) => {
                if (result.isConfirmed) {
                    blockUI();

                    const results = await onSaveJson(
                        `${baseUrl}/settings/users/${id}`,
                        JSON.stringify({
                            _token: csrf,
                        }),
                        "delete"
                    );

                    unBlockUI();

                    if (results.data.status) {
                        swalWithBootstrapButtons
                            .fire("success", results.data.message, "success")
                            .then(async (result) => {
                                if (result.isConfirmed) {
                                    fnUser.init.tables.tbUser.ajax
                                        .url(
                                            `${baseUrl}/settings/users/get-all-data`
                                        )
                                        .draw();
                                }
                            });
                    } else {
                        swalWithBootstrapButtons.fire(
                            "Failed",
                            results.data.message,
                            "error"
                        );
                    }
                }
            });
    },
};

fnUser.init.buttons.btnAdd.addEventListener("click", async () => {
    nameUser.value = "";
    usernameUser.value = "";
    idUser.value = "";
    passwordUser.value = "";

    if (passwordUser.classList.contains("d-none")) {
        passwordUser.classList.remove("d-none");
    }

    if (passwordLabelUser.classList.contains("d-none")) {
        passwordLabelUser.classList.remove("d-none");
    }

    if (usernameUser.readonly) {
        usernameUser.removeAttribute("readonly");
        usernameUser.classList.remove(["bg-gray-500"]);
    }

    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-homes`,
        fnUser.init.dropdowns.locationDropdown,
        "Pilih Lokasi Kos",
        ""
    );

    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-roles`,
        fnUser.init.dropdowns.roleDropdown,
        "Pilih Role",
        ""
    );

    fnUser.init.buttons.btnSave.setAttribute("data-type", "add-data");

    fnUser.init.modals.userModal.show();
});

fnUser.init.buttons.btnSave.addEventListener("click", async () => {
    switch (fnUser.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/settings/users`;

            data = JSON.stringify({
                name: nameUser.value,
                username: usernameUser.value,
                password: passwordUser.value,
                role: fnUser.init.dropdowns.roleDropdown.getValue(true),
                home: fnUser.init.dropdowns.locationDropdown.getValue(true),
                _token: fnUser.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/settings/users/${idUser.value}`;

            data = JSON.stringify({
                name: nameUser.value,
                username: usernameUser.value,
                role: fnUser.init.dropdowns.roleDropdown.getValue(true),
                home: fnUser.init.dropdowns.locationDropdown.getValue(true),
                _token: fnUser.init.buttons.btnSave.dataset.csrf,
            });

            method = "put";
            break;
    }

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swalWithBootstrapButtons
            .fire("Berhasil", results.data.message, "success")
            .then((result) => {
                if (result.isConfirmed) {
                    fnUser.init.modals.userModal.hide();

                    fnUser.init.tables.tbUser.ajax
                        .url(`${baseUrl}/settings/users/get-all-data`)
                        .draw();
                }
            });
    } else {
        if (results.data.message.name[0]) {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message.name[0],
                "error"
            );

            return false;
        }

        if (results.data.message.username[0]) {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message.username[0],
                "error"
            );

            return false;
        }

        if (results.data.message.home[0]) {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message.home[0],
                "error"
            );

            return false;
        }

        if (results.data.message.role[0]) {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message.role[0],
                "error"
            );

            return false;
        }

        if (results.data.message.password[0]) {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message.password[0],
                "error"
            );

            return false;
        }

        if (typeof results.data.message == "string") {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message,
                "error"
            );

            return false;
        }
    }
});
