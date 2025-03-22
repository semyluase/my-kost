const kodeItemPriceLaundryInput = document.querySelector(
    "#kode-item-price-laundry"
);
const typeLaundryInput = document.querySelector("#type-laundry");
const beratLaundryInput = document.querySelector("#berat-price-laundry");
const hargaLaundryInput = document.querySelector("#price-laundry");

const fnPriceLaundry = {
    init: {
        buttons: {
            btnAddPriceLaundry: document.querySelector(
                "#btn-add-price-laundry"
            ),
            btnSavePriceLaundry: document.querySelector(
                "#btn-save-price-laundry"
            ),
        },
        modals: {
            PriceLaundryModal: new bootstrap.Modal(
                document.querySelector("#modal-price-laundry")
            ),
        },
        tables: {
            tbPriceLaundry: $("#tb-price-laundry").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/laundry-price/get-all-data`,
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

        await fetch(`${baseUrl}/masters/laundry-price/${kodeItem}/edit`)
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
                unBlockUI();
                typeLaundryInput.value = response.name;
                beratLaundryInput.value = response.weight;
                hargaLaundryInput.value = response.price;
                kodeItemPriceLaundryInput.value = response.kode_item;
                fnPriceLaundry.init.buttons.btnSavePriceLaundry.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnPriceLaundry.init.modals.PriceLaundryModal.show();
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
                        `${baseUrl}/masters/laundry-price/${kodeItem}`,
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
                                    fnPriceLaundry.init.tables.tbPriceLaundry.ajax
                                        .url(
                                            `${baseUrl}/masters/laundry-price/get-all-data`
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

fnPriceLaundry.init.buttons.btnAddPriceLaundry.addEventListener(
    "click",
    async () => {
        typeLaundryInput.value = "";
        beratLaundryInput.value = "";
        hargaLaundryInput.value = "";
        fnPriceLaundry.init.buttons.btnSavePriceLaundry.setAttribute(
            "data-type",
            "add-data"
        );
        fnPriceLaundry.init.modals.PriceLaundryModal.show();
    }
);

fnPriceLaundry.init.buttons.btnSavePriceLaundry.addEventListener(
    "click",
    async () => {
        switch (fnPriceLaundry.init.buttons.btnSavePriceLaundry.dataset.type) {
            case "add-data":
                url = `${baseUrl}/masters/laundry-price`;

                data = JSON.stringify({
                    name: typeLaundryInput.value,
                    weight: beratLaundryInput.value,
                    harga: hargaLaundryInput.value,
                    _token: fnPriceLaundry.init.buttons.btnSavePriceLaundry
                        .dataset.csrf,
                });

                method = "post";
                break;

            case "edit-data":
                url = `${baseUrl}/masters/laundry-price/${kodeItemPriceLaundryInput.value}`;

                data = JSON.stringify({
                    name: typeLaundryInput.value,
                    weight: beratLaundryInput.value,
                    harga: hargaLaundryInput.value,
                    _token: fnPriceLaundry.init.buttons.btnSavePriceLaundry
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
                        fnPriceLaundry.init.modals.PriceLaundryModal.hide();
                        fnPriceLaundry.init.tables.tbPriceLaundry.ajax
                            .url(
                                `${baseUrl}/masters/laundry-price/get-all-data`
                            )
                            .draw();
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

            if (results.data.message.weight[0]) {
                swal.fire(
                    "Terjadi kesalahan",
                    results.data.message.weight[0],
                    "error"
                );
                return false;
            }

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
