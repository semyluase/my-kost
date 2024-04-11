const nameCategory = document.querySelector("#name-category");
const slugCategory = document.querySelector("#slug-category");

const fnCategory = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-category"),
            btnSave: document.querySelector("#btn-save-category"),
        },
        modals: {
            modalCategory: new bootstrap.Modal(
                document.querySelector("#modal-category")
            ),
        },
        tables: {
            tbCategory: $("#tb-category").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/categories/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },

    onActivated: async (slug, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/masters/categories/${slug}`,
            JSON.stringify({
                _token: csrf,
            }),
            "post"
        );

        unBlockUI();

        if (results.data.status) {
            swalWithBootstrapButtons
                .fire("Success", results.data.message, "success")
                .then((result) => {
                    if (result.isConfirmed) {
                        fnCategory.init.tables.tbCategory.ajax
                            .url(`${baseUrl}/masters/categories/get-all-data`)
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
    },

    onEdit: async (slug) => {
        await fetch(`${baseUrl}/masters/categories/${slug}/edit`)
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
            .then((response) => {
                nameCategory.value = response.name;
                slugCategory.value = response.slug;

                fnCategory.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnCategory.init.modals.modalCategory.show();
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
                        `${baseUrl}/masters/categories/${slug}`,
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
                                    fnCategory.init.tables.tbCategory.ajax
                                        .url(
                                            `${baseUrl}/masters/categories/get-all-data`
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

fnCategory.init.buttons.btnAdd.addEventListener("click", () => {
    nameCategory.value = "";

    fnCategory.init.buttons.btnSave.setAttribute("data-type", "add-data");
    fnCategory.init.modals.modalCategory.show();
});

fnCategory.init.buttons.btnSave.addEventListener("click", async () => {
    switch (fnCategory.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/categories`;

            data = JSON.stringify({
                name: nameCategory.value,
                _token: fnCategory.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/categories/${slugCategory.value}`;

            data = JSON.stringify({
                name: nameCategory.value,
                _token: fnCategory.init.buttons.btnSave.dataset.csrf,
            });

            method = "put";
            break;
    }

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swalWithBootstrapButtons
            .fire("Success", results.data.message, "success")
            .then((result) => {
                if (result.isConfirmed) {
                    fnCategory.init.modals.modalCategory.hide();

                    fnCategory.init.tables.tbCategory.ajax
                        .url(`${baseUrl}/masters/categories/get-all-data`)
                        .draw();
                }
            });
    } else {
        if (results.data.message.name[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.name[0],
                "error"
            );

            return false;
        }

        if (results.data.message.slug[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.slug[0],
                "error"
            );

            return false;
        }

        if (results.data.message.city[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.city[0],
                "error"
            );

            return false;
        }

        if (results.data.message.address[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.address[0],
                "error"
            );

            return false;
        }

        if (typeof results.data.message == "string") {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message,
                "error"
            );

            return false;
        }
    }
});
