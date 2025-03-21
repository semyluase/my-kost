const kodeItemPriceCleaningInput = document.querySelector(
    "#kode-item-price-cleaning"
);
const hargaCleaningInput = document.querySelector("#price-cleaning");

const fnPriceCleaning = {
    init: {
        buttons: {
            btnAddPriceCleaning: document.querySelector(
                "#btn-add-price-cleaning"
            ),
            btnSavePriceCleaning: document.querySelector(
                "#btn-save-price-cleaning"
            ),
        },
        modals: {
            priceCleaningModal: new bootstrap.Modal(
                document.querySelector("#modal-price-cleaning")
            ),
        },
        tables: {
            tbPriceCleaning: $("#tb-price-cleaning").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/cleaning-price/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },

    onEdit: async (kodeItem) => {
        blockUI();

        await fetch(`${baseUrl}/masters/cleaning-price/${kodeItem}/edit`)
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
            .then(async (response) => {
                hargaCleaningInput.value = response.price;
                kodeItemPriceCleaningInput.value = response.kode_item;
                fnPriceCleaning.init.buttons.btnSavePriceCleaning.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnPriceCleaning.init.modals.priceCleaningModal.show();
            });
    },

    onDelete: async (kodeItem, csrf) => {
        swalWithBootstrapButtons
            .fire({
                title: "Apakah anda yakin?",
                text: "Anda akan menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "Tidak",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
            })
            .then(async (result) => {
                if (result.isConfirmed) {
                    blockUI();

                    const results = await onSaveJson(
                        `${baseUrl}/masters/cleaning-price/${kodeItem}`,
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
                                    fnPriceCleaning.init.tables.tbPriceCleaning.ajax
                                        .url(
                                            `${baseUrl}/masters/cleaning-price/get-all-data`
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

fnPriceCleaning.init.buttons.btnAddPriceCleaning.addEventListener(
    "click",
    async () => {
        hargaCleaningInput.value = "";
        fnPriceCleaning.init.buttons.btnSavePriceCleaning.setAttribute(
            "data-type",
            "add-data"
        );
        fnPriceCleaning.init.modals.priceCleaningModal.show();
    }
);

fnPriceCleaning.init.buttons.btnSavePriceCleaning.addEventListener(
    "click",
    async () => {
        switch (
            fnPriceCleaning.init.buttons.btnSavePriceCleaning.dataset.type
        ) {
            case "add-data":
                url = `${baseUrl}/masters/cleaning-price`;

                data = JSON.stringify({
                    harga: hargaCleaningInput.value,
                    _token: fnPriceCleaning.init.buttons.btnSavePriceCleaning
                        .dataset.csrf,
                });

                method = "post";
                break;

            case "edit-data":
                url = `${baseUrl}/masters/cleaning-price/${kodeItemPriceCleaningInput.value}`;

                data = JSON.stringify({
                    harga: hargaCleaningInput.value,
                    _token: fnPriceCleaning.init.buttons.btnSavePriceCleaning
                        .dataset.csrf,
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
                        fnPriceCleaning.init.modals.priceCleaningModal.hide();
                        fnPriceCleaning.init.tables.tbPriceCleaning.ajax
                            .url(
                                `${baseUrl}/masters/cleaning-price/get-all-data`
                            )
                            .draw();
                    }
                }
            );
        } else {
            if (results.data.message.harga[0]) {
                swal.fire(
                    "Terjadi kesalahan",
                    results.data.message.harga[0],
                    "error"
                );
                return false;
            }

            if (typeof results.data.message == "string") {
                swal.fire("Terjadi kesalahan", results.data.message, "error");
                return false;
            }
        }
    }
);
