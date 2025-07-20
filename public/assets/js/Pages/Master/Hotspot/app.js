const ssidInput = document.querySelector("#ssid-hotspot");
const passwordInput = document.querySelector("#password-hotspot");
const ssidEditInput = document.querySelector("#ssid-hotspot-edit");
const idEditInput = document.querySelector("#id-hotspot-edit");
const passwordEditInput = document.querySelector("#password-hotspot-edit");

// let url, data, method;

const fnHotspot = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-hotspot"),
            btnSave: document.querySelector("#btn-save-hotspot"),
            btnSaveEdit: document.querySelector("#btn-save-hotspot-edit"),
        },
        dropdowns: {
            roomDropdown: new Choices(document.querySelector("#room-hotspot"), {
                removeItemButton: true,
            }),
            roomEditDropdown: new Choices(
                document.querySelector("#room-hotspot-edit")
            ),
        },
        modals: {
            modalHotspot: new bootstrap.Modal(
                document.querySelector("#modal-hotspot")
            ),
            modalEditHotspot: new bootstrap.Modal(
                document.querySelector("#modal-hotspot-edit")
            ),
        },
        tables: {
            tbHotspot: $("#tb-hotspot").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/masters/hotspots/get-all-data`,
                },
            }),
        },
    },

    onEdit: async (id) => {
        await fetch(`${baseUrl}/masters/hotspots/${id}/edit`)
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
                idEditInput.value = response.id;
                ssidEditInput.value = response.ssid;
                passwordEditInput.value = response.password;

                await createDropdown(
                    `${baseUrl}/utils/dropdowns/get-room`,
                    fnHotspot.init.dropdowns.roomEditDropdown,
                    "",
                    response.room_number
                );

                fnHotspot.init.modals.modalEditHotspot.show();
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
                        `${baseUrl}/masters/hotspots/${id}`,
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
                                    fnHotspot.init.tables.tbHotspot.ajax
                                        .url(
                                            `${baseUrl}/masters/hotspots/get-all-data`
                                        )
                                        .load();
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

fnHotspot.init.buttons.btnAdd.addEventListener("click", async () => {
    ssidInput.value = "";
    passwordInput.value = "";

    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-room`,
        fnHotspot.init.dropdowns.roomDropdown,
        "",
        ""
    );

    fnHotspot.init.modals.modalHotspot.show();
});

fnHotspot.init.buttons.btnSave.addEventListener("click", async () => {
    url = `${baseUrl}/masters/hotspots`;

    data = JSON.stringify({
        ssid: ssidInput.value,
        password: passwordInput.value,
        rooms: fnHotspot.init.dropdowns.roomDropdown.getValue(true),
        _token: fnHotspot.init.buttons.btnSave.dataset.csrf,
    });

    method = "post";

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swal.fire("Berhasil", results.data.message, "success");

        fnHotspot.init.tables.tbHotspot.ajax
            .url(`${baseUrl}/masters/hotspots/get-all-data`)
            .load();

        fnHotspot.init.modals.modalHotspot.hide();
    } else {
        if (results.data.message.ssid) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.ssid[0],
                "error"
            );

            return false;
        }

        if (results.data.message.password) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.password[0],
                "error"
            );

            return false;
        }

        if (results.data.message.rooms) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.rooms[0],
                "error"
            );

            return false;
        }

        if (typeof results.data.message == "string") {
            swal.fire("Terjadi kesalahan", results.data.message, "error");

            return false;
        }
    }
});

fnHotspot.init.buttons.btnSaveEdit.addEventListener("click", async () => {
    url = `${baseUrl}/masters/hotspots/${idEditInput.value}`;

    data = JSON.stringify({
        ssid: ssidEditInput.value,
        password: passwordEditInput.value,
        room: fnHotspot.init.dropdowns.roomEditDropdown.getValue(true),
        _token: fnHotspot.init.buttons.btnSaveEdit.dataset.csrf,
    });

    method = "put";

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swal.fire("Berhasil", results.data.message, "success");

        fnHotspot.init.tables.tbHotspot.ajax
            .url(`${baseUrl}/masters/hotspots/get-all-data`)
            .load();

        fnHotspot.init.modals.modalEditHotspot.hide();
    } else {
        if (results.data.message.ssid) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.ssid[0],
                "error"
            );

            return false;
        }

        if (results.data.message.password) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.password[0],
                "error"
            );

            return false;
        }

        if (results.data.message.rooms) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.rooms[0],
                "error"
            );

            return false;
        }

        if (typeof results.data.message == "string") {
            swal.fire("Terjadi kesalahan", results.data.message, "error");

            return false;
        }
    }
});
