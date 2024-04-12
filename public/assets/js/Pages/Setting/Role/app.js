const nameRole = document.querySelector("#name");
const slugRole = document.querySelector("#slug");

let url, data, method;
const fnRole = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add"),
            btnSave: document.querySelector("#btn-save"),
        },
        modals: {
            modalRole: new bootstrap.Modal(
                document.querySelector("#modal-role")
            ),
        },
        tables: {
            tbRole: $("#tb-role").DataTable({
                ajax: {
                    url: `${baseUrl}/settings/roles/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
            }),
        },
    },

    onEdit: async (slug) => {
        await fetch(`${baseUrl}/settings/roles/${slug}/edit`)
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
                nameRole.value = response.name;
                slugRole.value = response.slug;

                fnRole.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnRole.init.modals.modalRole.show();
            });
    },

    onDelete: async (slug, csrf) => {
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
                        `${baseUrl}/settings/roles/${slug}`,
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
                                    fnFoodSnack.init.tables.tbFoodSnack.ajax
                                        .url(
                                            `${baseUrl}/settings/roles/get-all-data`
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

fnRole.init.buttons.btnAdd.addEventListener("click", () => {
    nameRole.value = "";
    slugRole.value = "";

    fnRole.init.buttons.btnSave.setAttribute("data-type", "add-data");

    fnRole.init.modals.modalRole.show();
});

fnRole.init.buttons.btnSave.addEventListener("click", async () => {
    switch (fnRole.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/settings/roles`;

            data = JSON.stringify({
                name: nameRole.value,
                _token: fnRole.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/settings/roles/${slugRole.value}`;

            data = JSON.stringify({
                name: nameRole.value,
                _token: fnRole.init.buttons.btnSave.dataset.csrf,
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
                    fnRole.init.modals.modalRole.hide();
                    fnRole.init.tables.tbRole.ajax
                        .url(`${baseUrl}/settings/roles/get-all-data`)
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

        if (results.data.message.slug[0]) {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message.slug[0],
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
