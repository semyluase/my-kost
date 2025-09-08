const nameCategoryItem = document.querySelector("#name-category-item");
const slugCategoryItem = document.querySelector("#slug-category-item");
const codeCategoryItem = document.querySelector("#code-category-item");

const fnCategoryItem = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-category-order"),
            btnSave: document.querySelector("#btn-save-category-item"),
        },
        modals: {
            modalCategoryItem: new bootstrap.Modal(
                document.querySelector("#modal-category-item")
            ),
        },
        tables: {
            tbCategoryItem: $("#tb-category-order").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/category-orders/get-all-data`,
                },
                processing: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },

    onEdit: async (slug) => {
        await fetch(`${baseUrl}/masters/category-orders/${slug}/edit`)
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
                nameCategoryItem.value = response.name;
                slugCategoryItem.value = response.slug;
                codeCategoryItem.value = response.short_name;

                fnCategoryItem.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnCategoryItem.init.modals.modalCategoryItem.show();
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
                        `${baseUrl}/masters/category-orders/${slug}`,
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
                                    fnCategoryItem.init.tables.tbCategoryItem.ajax
                                        .url(
                                            `${baseUrl}/masters/category-orders/get-all-data`
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

fnCategoryItem.init.buttons.btnAdd.addEventListener("click", async () => {
    nameCategoryItem.value = "";
    slugCategoryItem.value = "";
    codeCategoryItem.value = "";

    fnCategoryItem.init.buttons.btnSave.setAttribute("data-type", "add-data");
    fnCategoryItem.init.modals.modalCategoryItem.show();
});

fnCategoryItem.init.buttons.btnSave.addEventListener("click", async () => {
    switch (fnCategoryItem.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/category-orders`;

            data = JSON.stringify({
                name: nameCategoryItem.value,
                code: codeCategoryItem.value,
                _token: fnCategoryItem.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/category-orders/${slugCategoryItem.value}`;

            data = JSON.stringify({
                name: nameCategoryItem.value,
                code: codeCategoryItem.value,
                _token: fnCategoryItem.init.buttons.btnSave.dataset.csrf,
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
                    fnCategoryItem.init.modals.modalCategoryItem.hide();

                    fnCategoryItem.init.tables.tbCategoryItem.ajax
                        .url(`${baseUrl}/masters/category-orders/get-all-data`)
                        .load();
                }
            });
    } else {
        if (results.data.message.name) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.name[0],
                "error"
            );

            return false;
        }

        if (results.data.message.code) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.code[0],
                "error"
            );

            return false;
        }

        if (results.data.message.slug) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.slug[0],
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
