const nameRulesInput = document.querySelector("#name-rules");
const slugRulesInput = document.querySelector("#slug-rules");

const fnAturan = {
    init: {
        buttons: {
            btnAddRules: document.querySelector("#btn-add-rules"),
            btnSaveRules: document.querySelector("#btn-save-rules"),
        },
        modals: {
            modalRules: new bootstrap.Modal(
                document.querySelector("#modal-rules")
            ),
        },
        tables: {
            tbAturan: $("#tb-aturan").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/rules/get-all-data`,
                },
                processing: true,
                ordering: false,
                scrollX: true,
                rowReorder: true,
                paging: false,
            }),
        },
    },

    onDragRow: async (id, csrf) => {
        console.log(true);
    },

    onEdit: async (slug) => {
        blockUI();
        await fetch(`${baseUrl}/masters/rules/${slug}/edit`)
            .then((response) => {
                if (!response.ok) {
                    unBlockUI();
                    throw new Error(
                        swal.fire(
                            "Terjadi kesalahan",
                            "Saat pengambilan data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                nameRulesInput.value = response.name;
                slugRulesInput.value = response.slug;

                fnAturan.init.buttons.btnSaveRules.setAttribute(
                    "data-type",
                    "edit-data"
                );
                unBlockUI();
                fnAturan.init.modals.modalRules.show();
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
                        `${baseUrl}/masters/rules/${slug}`,
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
                                    fnAturan.init.tables.tbAturan.ajax
                                        .url(
                                            `${baseUrl}/masters/rules/get-all-data`
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

fnAturan.init.buttons.btnAddRules.addEventListener("click", () => {
    nameRulesInput.value = "";
    slugRulesInput.value = "";

    fnAturan.init.buttons.btnSaveRules.setAttribute("data-type", "add-data");
    fnAturan.init.modals.modalRules.show();
});
fnAturan.init.buttons.btnSaveRules.addEventListener("click", async () => {
    switch (fnAturan.init.buttons.btnSaveRules.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/rules`;

            data = JSON.stringify({
                name: nameRulesInput.value,
                _token: fnAturan.init.buttons.btnSaveRules.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/rules/${slugRulesInput.value}`;

            data = JSON.stringify({
                name: nameRulesInput.value,
                _token: fnAturan.init.buttons.btnSaveRules.dataset.csrf,
            });

            method = "put";
            break;
    }

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swal.fire("Berhasil", results.data.message, "success").then(
            (result) => {
                if (result.isConfirmed) {
                    fnAturan.init.modals.modalRules.hide();
                    fnAturan.init.tables.tbAturan.ajax
                        .url(`${baseUrl}/masters/rules/get-all-data`)
                        .load();
                }
            }
        );
    } else {
        if (results.data.message.name[0]) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.name[0],
                "error"
            );
            return false;
        }

        if (results.data.message.slug[0]) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.slug[0],
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

fnAturan.init.tables.tbAturan.on("row-reorder", async function (e, diff, edit) {
    let reorder = [];
    let csrf = fnAturan.init.tables.tbAturan.row().data()[4];
    for (let i = 0, ien = diff.length; i < ien; i++) {
        let rowData = fnAturan.init.tables.tbAturan.row(diff[i].node).data();
        reorder.push({
            id: rowData[3],
            newPosition: diff[i].newPosition,
            oldPosition: diff[i].oldPosition,
        });
    }

    url = `${baseUrl}/masters/rules/re-order`;

    data = JSON.stringify({
        dataReorder: reorder,
        _token: csrf,
    });

    method = "put";

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        fnAturan.init.tables.tbAturan.ajax
            .url(`${baseUrl}/masters/rules/get-all-data`)
            .load();
    } else {
        if (typeof results.data.message == "string") {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
            return false;
        }
    }
});
