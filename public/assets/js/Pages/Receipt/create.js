const nobuktiInput = document.querySelector("#nobukti");
const idDetailInput = document.querySelector("#id-detail");
const kodebrgInput = document.querySelector("#kodebrg");
const kategoriInput = document.querySelector("#kategori");
const namabrgInput = document.querySelector("#namabrg");
const jumlahInput = document.querySelector("#jumlah");
const hargaBeliInput = document.querySelector("#harga-beli");
const subTotalInput = document.querySelector("#sub-total");

let url, data, method;

const fnReceipt = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
            btnPosting: document.querySelector("#btn-posting"),
            btnDeleteBulk: document.querySelector("#btn-delete-bulk"),
        },
        litepicker: {
            transDate: new Litepicker({
                element: document.querySelector("#tgl"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                startDate: moment().format("DD/MM/YYYY"),
                format: "DD/MM/YYYY",
                inlineMode: true,
                singleMode: true,
            }),
        },
        tables: {
            tbDetail: $("#tb-detail").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/inventories/receipts/get-detail-data?nobukti=${nobuktiInput.value}`,
                },
            }),
        },
    },

    onClearForm: () => {
        kodebrgInput.value = "";
        idDetailInput.value = "";
        namabrgInput.value = "";
        kategoriInput.value = "";
        jumlahInput.value = "";
        hargaBeliInput.value = "";
        subTotalInput.value = "";
        kodebrgInput.focus();
    },

    onSelectGoods: (codeItem, name, category) => {
        kodebrgInput.value = codeItem;
        namabrgInput.value = name;
        kategoriInput.value = category;
    },

    onDeleteDetail: (id, csrf) => {
        swalWithBootstrapButtons
            .fire({
                title: "Perhatian",
                text: "Apakah anda akan menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 12l5 5l10 -10"></path>
         </svg> Hapus Data`,
                cancelButtonText: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M18 6l-12 12"></path>
            <path d="M6 6l12 12"></path>
         </svg> Batal`,
            })
            .then(async (result) => {
                if (result.isConfirmed) {
                    blockUI();

                    const results = await onSaveJson(
                        `${baseUrl}/inventories/receipts/details/${id}`,
                        JSON.stringify({ _token: csrf }),
                        "delete"
                    );

                    unBlockUI();

                    if (results.data.status) {
                        Toastify({
                            text: results.data.message,
                            className: "success",
                            style: {
                                background: "rgb(47, 179, 68)",
                            },
                        }).showToast();

                        fnReceipt.init.tables.tbDetail.ajax
                            .url(
                                `${baseUrl}/inventories/receipts/get-detail-data?nobukti=${nobuktiInput.value}`
                            )
                            .load();
                    } else {
                        if (typeof results.data.message == "string") {
                            swalWithBootstrapButtons.fire(
                                "Terjadi Kesalahan",
                                results.data.message,
                                "error"
                            );
                        }
                    }
                }
            });
    },
};

jumlahInput.addEventListener("keyup", (e) => {
    subTotalInput.value =
        parseInt(jumlahInput.value) * parseInt(hargaBeliInput.value);
});

hargaBeliInput.addEventListener("keyup", (e) => {
    subTotalInput.value =
        parseInt(jumlahInput.value) * parseInt(hargaBeliInput.value);
});

fnReceipt.init.buttons.btnSave.addEventListener("click", async () => {
    blockUI();

    if (idDetailInput.value == "") {
        url = `${baseUrl}/inventories/receipts`;
        data = JSON.stringify({
            nobukti: nobuktiInput.value,
            kodebrg: kodebrgInput.value,
            kategori: kategoriInput.value,
            namabrg: namabrgInput.value,
            jumlah: jumlahInput.value,
            hargaBeli: hargaBeliInput.value,
            tanggal: moment(
                fnReceipt.init.litepicker.transDate.getDate().toJSDate()
            ).format("YYYY-MM-DD"),
            _token: fnReceipt.init.buttons.btnSave.dataset.csrf,
        });
        method = "post";
    } else {
        url = `${baseUrl}/inventories/receipts/${idDetailInput.value}`;
        data = JSON.stringify({
            nobukti: nobuktiInput.value,
            kodebrg: kodebrgInput.value,
            kategori: kategoriInput.value,
            namabrg: namabrgInput.value,
            jumlah: jumlahInput.value,
            hargaBeli: hargaBeliInput.value,
            _token: fnReceipt.init.buttons.btnSave.dataset.csrf,
        });
        method = "put";
    }

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        Toastify({
            text: results.data.message,
            className: "success",
            style: {
                background: "rgb(47, 179, 68)",
            },
        }).showToast();

        if (nobuktiInput.value == "") {
            window.history.pushState(
                null,
                null,
                `${baseUrl}/inventories/receipts/create?nobukti=${results.data.nobukti}`
            );
            nobuktiInput.value = results.data.nobukti;
        }

        fnReceipt.init.tables.tbDetail.ajax
            .url(
                `${baseUrl}/inventories/receipts/get-detail-data?nobukti=${nobuktiInput.value}`
            )
            .load();

        fnReceipt.onClearForm();
    } else {
        swal.fire("Terjadi kesalahan", results.data.message, "error");
    }
});

fnReceipt.init.buttons.btnDeleteBulk.addEventListener("click", async () => {
    swalWithBootstrapButtons
        .fire({
            title: "Perhatian",
            text: "Apakah anda akan menghapus semua data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 12l5 5l10 -10"></path>
         </svg> Hapus Data`,
            cancelButtonText: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M18 6l-12 12"></path>
            <path d="M6 6l12 12"></path>
         </svg> Batal`,
        })
        .then(async (result) => {
            if (result.isConfirmed) {
                blockUI();

                const results = await onSaveJson(
                    `${baseUrl}/inventories/receipts/${nobuktiInput.value}`,
                    JSON.stringify({
                        nobukti: nobuktiInput.value,
                        _token: fnReceipt.init.buttons.btnDeleteBulk.dataset
                            .csrf,
                    }),
                    "delete"
                );

                unBlockUI();

                if (results.data.status) {
                    swal.fire("Berhasil", results.data.message, "success").then(
                        (result) => {
                            if (result.isConfirmed) {
                                window.location.href = `${baseUrl}/inventories/receipts`;
                            }
                        }
                    );
                } else {
                    if (typeof results.data.message == "string") {
                        swalWithBootstrapButtons.fire(
                            "Terjadi Kesalahan",
                            results.data.message,
                            "error"
                        );
                    }
                }
            }
        });
});

fnReceipt.init.buttons.btnPosting.addEventListener("click", async () => {
    swalWithBootstrapButtons
        .fire({
            title: "Perhatian",
            text: "Apakah anda yakin akan memposting transaksi ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 12l5 5l10 -10"></path>
         </svg> Posting Data`,
            cancelButtonText: `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M18 6l-12 12"></path>
            <path d="M6 6l12 12"></path>
         </svg> Batal`,
        })
        .then(async (result) => {
            if (result.isConfirmed) {
                blockUI();

                const results = await onSaveJson(
                    `${baseUrl}/inventories/receipts/posting`,
                    JSON.stringify({
                        nobukti: nobuktiInput.value,
                        _token: fnReceipt.init.buttons.btnPosting.dataset.csrf,
                    }),
                    "post"
                );

                unBlockUI();

                if (results.data.status) {
                    swal.fire("Berhasil", results.data.message, "success").then(
                        (result) => {
                            if (result.isConfirmed) {
                                window.location.href = `${baseUrl}/inventories/receipts`;
                            }
                        }
                    );
                } else {
                    if (typeof results.data.message == "string") {
                        swalWithBootstrapButtons.fire(
                            "Terjadi Kesalahan",
                            results.data.message,
                            "error"
                        );
                    }
                }
            }
        });
});
