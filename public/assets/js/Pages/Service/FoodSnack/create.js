const listMenu = document.querySelector("#list-menu");
const paymentSection = document.querySelector("#payment-section");

const nobuktiInput = document.querySelector("#nobukti");
const kodeBarangInput = document.querySelector("#kode-barang");
const namaBarangInput = document.querySelector("#nama-barang");
const hargaBarangInput = document.querySelector("#harga-barang");
const jumlahInput = document.querySelector("#jumlah-barang");

const tipePaymentSelect = document.querySelectorAll("#tipe-pembayaran");

const jumlahBayarInput = document.querySelector("#jumlah-pembayaran");
const kembalianInput = document.querySelector("#kembalian");

let qtyBarang = 0,
    tipePayment,
    totalHarga,
    url,
    data,
    method;

const fnCreateFoodSnack = {
    init: {
        buttons: {
            btnCariBarang: document.querySelector("#btn-search-barang"),
            btnKurangJumlah: document.querySelector("#btn-kurang-jumlah"),
            btnTambahJumlah: document.querySelector("#btn-tambah-jumlah"),
            btnPembayaran: document.querySelector("#btn-pembayaran"),
            btnSave: document.querySelector("#btn-save"),
            btnProsesPembayaran: document.querySelector("#btn-save-payment"),
        },
        dropdowns: {
            noKamarDropdown: new Choices(document.querySelector("#no-kamar"), {
                shouldSort: false,
            }),
        },
        offCanvas: {
            listMenuOffCanvas: new bootstrap.Offcanvas(
                document.querySelector("#offcanvasMenu")
            ),
            paymentOffCanvas: new bootstrap.Offcanvas(
                document.querySelector("#offcanvasPayment")
            ),
        },
        tables: {
            tbDetail: $("#tb-detail").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/transactions/orders/food-snack/receipt?nobukti=${nobukti}`,
                },
            }),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-room`,
            fnCreateFoodSnack.init.dropdowns.noKamarDropdown,
            "Pilih Kamar",
            noKamar
        );

        await fetch(`${baseUrl}/transactions/orders/food-snack/get-list-menu`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        swal.fire(
                            "Terjadi kesalahan",
                            "Saat mengambil data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                listMenu.innerHTML = response;
            });
    },

    onSelectBarang: async (codeItem, namaBarang, harga, qty) => {
        kodeBarangInput.value = codeItem;
        namaBarangInput.value = namaBarang;
        hargaBarangInput.value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(harga);

        jumlahInput.value = 1;

        qtyBarang = qty;

        fnCreateFoodSnack.init.offCanvas.listMenuOffCanvas.hide();
    },

    onEditReceipt: async (id) => {
        blockUI();
        await fetch(`${baseUrl}/transactions/orders/food-snack/${id}/edit`)
            .then((response) => {
                if (!response.ok) {
                    unBlockUI();
                    throw new Error(
                        swal.fire("Terjadi kesalahan", "Saat pengambilan data")
                    );
                }

                return response.json();
            })
            .then((response) => {
                kodeBarangInput.value = response.code_item;
                namaBarangInput.value = response.food_snack.name;
                hargaBarangInput.value = new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                }).format(response.harga_jual);
                qtyBarang = response.stock.qty;
                jumlahInput.value = response.qty;

                fnCreateFoodSnack.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                unBlockUI();
            });
    },

    onDeleteReceipt: async (id, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/transactions/orders/food-snack/${id}`,
            JSON.stringify({
                _token: csrf,
            }),
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

            fnCreateFoodSnack.init.tables.tbDetail.ajax
                .url(
                    `${baseUrl}/transactions/orders/food-snack/receipt?nobukti=${nobuktiInput.value}`
                )
                .load();
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
        }
    },
};

fnCreateFoodSnack.onLoad();

fnCreateFoodSnack.init.buttons.btnCariBarang.addEventListener(
    "click",
    async () => {
        fnCreateFoodSnack.init.offCanvas.listMenuOffCanvas.show();
    }
);

fnCreateFoodSnack.init.buttons.btnPembayaran.addEventListener(
    "click",
    async () => {
        blockUI();
        await fetch(
            `${baseUrl}/transactions/orders/food-snack/payments/${nobuktiInput.value}`
        )
            .then((response) => {
                if (!response.ok) {
                    unBlockUI();

                    throw new Error(
                        swal.fire(
                            "Terjadi Kesalahan",
                            "saat pengambilan data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                unBlockUI();
                fnCreateFoodSnack.init.offCanvas.paymentOffCanvas.show();
                paymentSection.innerHTML = response;

                totalHarga = document.querySelector("#total-harga-payment");
                tipePaymentSelect.forEach((item) => {
                    if (item.checked) {
                        switch (item.value) {
                            case "transfer":
                            case "saldo":
                                jumlahBayarInput.value = parseInt(
                                    totalHarga.textContent.replace(
                                        /[^0-9-,]/g,
                                        ""
                                    )
                                );
                                kembalian =
                                    jumlahBayarInput.value -
                                    parseInt(
                                        totalHarga.textContent.replace(
                                            /[^0-9-,]/g,
                                            ""
                                        )
                                    );

                                if (kembalian >= 0) {
                                    kembalianInput.value =
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(kembalian);
                                }
                                break;

                            default:
                                jumlahBayarInput.value = "";
                                kembalianInput.value = "";
                                break;
                        }
                    }
                });
            });
    }
);

fnCreateFoodSnack.init.buttons.btnKurangJumlah.addEventListener("click", () => {
    if (jumlahInput.value > 0) {
        jumlahInput.value--;
    }
});

fnCreateFoodSnack.init.buttons.btnTambahJumlah.addEventListener("click", () => {
    if (jumlahInput.value <= qtyBarang) {
        jumlahInput.value++;
    }
});

tipePaymentSelect.forEach((item) => {
    item.addEventListener("click", () => {
        switch (item.value) {
            case "transfer":
            case "saldo":
                jumlahBayarInput.value = parseInt(
                    totalHarga.textContent.replace(/[^0-9-,]/g, "")
                );
                kembalian =
                    jumlahBayarInput.value -
                    parseInt(totalHarga.textContent.replace(/[^0-9-,]/g, ""));
                if (kembalian >= 0) {
                    kembalianInput.value = new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    }).format(kembalian);
                }

                break;

            default:
                jumlahBayarInput.value = "";
                kembalianInput.value = "";
                break;
        }
    });
});

jumlahBayarInput.addEventListener("keyup", () => {
    kembalian =
        jumlahBayarInput.value -
        parseInt(totalHarga.textContent.replace(/[^0-9-,]/g, ""));

    if (kembalian >= 0) {
        kembalianInput.value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(kembalian);
    }
});

fnCreateFoodSnack.init.buttons.btnSave.addEventListener("click", async () => {
    switch (fnCreateFoodSnack.init.buttons.btnSave.dataset.type) {
        case "save-data":
            url = `${baseUrl}/transactions/orders/food-snack`;

            data = JSON.stringify({
                nobukti: nobuktiInput.value,
                noKamar:
                    fnCreateFoodSnack.init.dropdowns.noKamarDropdown.getValue(
                        true
                    ),
                kodeBarang: kodeBarangInput.value,
                jumlah: jumlahInput.value,
                _token: fnCreateFoodSnack.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/transactions/orders/food-snack`;

            data = JSON.stringify({
                nobukti: nobuktiInput.value,
                kodeBarang: kodeBarangInput.value,
                jumlah: jumlahInput.value,
                _token: fnCreateFoodSnack.init.buttons.btnSave.dataset.csrf,
            });

            method = "put";
            break;
    }

    blockUI();

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

        kodeBarangInput.value = "";
        namaBarangInput.value = "";
        hargaBarangInput.value = "";
        jumlahInput.value = "";

        if (nobuktiInput.value == "") {
            location.href = `${baseUrl}/transactions/orders/food-snack/create?nobukti=${results.data.nobukti}`;

            nobuktiInput.value = results.data.nobukti;
        }

        fnCreateFoodSnack.init.tables.tbDetail.ajax
            .url(
                `${baseUrl}/transactions/orders/food-snack/receipt?nobukti=${nobuktiInput.value}`
            )
            .load();
    } else {
        if (results.data.message.noKamar) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.noKamar[0],
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

fnCreateFoodSnack.init.buttons.btnProsesPembayaran.addEventListener(
    "click",
    async () => {
        console.log(totalHarga);

        url = `${baseUrl}/transactions/orders/food-snack/payments`;

        Array.from(tipePaymentSelect).forEach((item) => {
            if (item.checked) {
                tipePayment = item.value;
            }
        });

        data = JSON.stringify({
            nobukti: nobuktiInput.value,
            tipePayment: tipePayment,
            payment: parseInt(jumlahBayarInput.value),
            totalharga: parseInt(
                totalHarga.textContent.replace(/[^0-9-,]/g, "")
            ),
            kembalian: parseInt(kembalianInput.value.replace(/[^0-9-,]/g, "")),
            _token: fnCreateFoodSnack.init.buttons.btnProsesPembayaran.dataset
                .csrf,
        });

        method = "post";

        blockUI();

        const results = await onSaveJson(url, data, method);

        unBlockUI();

        if (results.data.status) {
            fnCreateFoodSnack.init.offCanvas.paymentOffCanvas.hide();
            swal.fire("Berhasil", results.data.message, "success").then(
                (result) => {
                    if (result.isConfirmed) {
                        window.location.href = `${baseUrl}/transactions/orders`;
                    }
                }
            );
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
        }
    }
);
