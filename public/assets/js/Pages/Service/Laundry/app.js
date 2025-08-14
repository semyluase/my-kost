const nobuktiInput = document.querySelector("#nobukti");
const categoryInput = document.querySelector("#category");
const qtyInput = document.querySelector("#qty");
const priceInput = document.querySelector("#price");
const totalBayarInput = document.querySelector("#total-bayar");
const totalKembaliInput = document.querySelector("#total-kembali");

const payment = document.querySelectorAll("#payment");

let startDate = moment().startOf("month"),
    endDate = moment(),
    categoryPayment;

const fnLaundry = {
    init: {
        buttons: {
            btnSeacrh: document.querySelector("#btn-search"),
            btnSavePayment: document.querySelector("#btn-payment"),
        },
        litepicker: {
            transDate: new Litepicker({
                element: document.querySelector("#datepicker-icon"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                startDate: startDate,
                endDate: endDate,
                format: "DD/MM/YYYY",
                singleMode: false,
            }),
        },
        modals: {
            modalLaundryPayment: new bootstrap.Modal(
                document.querySelector("#modal-laundry-payment")
            ),
        },
        tables: {
            tbLaundry: $("#tb-laundry").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/transactions/orders/laundry/get-all-data?s=${startDate.format(
                        "YYYY-MM-DD"
                    )}&e${endDate.format("YYYY-MM-DD")}`,
                },
            }),
        },
    },

    receiveLaundry: async (nobukti, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/transactions/orders/laundry/receive-laundry`,
            JSON.stringify({
                nobukti: nobukti,
                _token: csrf,
            }),
            "post"
        );

        unBlockUI();

        if (results.data.status) {
            swal.fire("Berhasil", results.data.message, "success");
            fnLaundry.init.tables.tbLaundry.ajax
                .url(
                    `${baseUrl}/transactions/orders/laundry/get-all-data?s=${startDate.format(
                        "YYYY-MM-DD"
                    )}&e${endDate.format("YYYY-MM-DD")}`
                )
                .load();
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");

            return false;
        }
    },

    finishLaundry: async (nobukti, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/transactions/orders/laundry/finish-laundry`,
            JSON.stringify({
                nobukti: nobukti,
                _token: csrf,
            }),
            "post"
        );

        unBlockUI();

        if (results.data.status) {
            swal.fire("Berhasil", results.data.message, "success");
            fnLaundry.init.tables.tbLaundry.ajax
                .url(
                    `${baseUrl}/transactions/orders/laundry/get-all-data?s=${startDate.format(
                        "YYYY-MM-DD"
                    )}&e${endDate.format("YYYY-MM-DD")}`
                )
                .load();
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");

            return false;
        }
    },

    onTakeLaundry: async (nobukti, payment, csrf) => {
        if (payment == 1) {
            blockUI();

            const results = await onSaveJson(
                `${baseUrl}/transactions/orders/laundry/take-laundry`,
                JSON.stringify({
                    nobukti: nobukti,
                    _token: csrf,
                }),
                "post"
            );

            unBlockUI();

            if (results.data.status) {
                swal.fire("Berhasil", results.data.message, "success");

                fnLaundry.init.tables.tbLaundry.ajax
                    .url(
                        `${baseUrl}/transactions/orders/laundry/get-all-data?s=${startDate.format(
                            "YYYY-MM-DD"
                        )}&e${endDate.format("YYYY-MM-DD")}`
                    )
                    .load();
            } else {
                swal.fire("Terjadi kesalahan", results.data.message, "error");
                return false;
            }
        } else {
            window.location.href = `${baseUrl}/transactions/orders/laundry/create?nobukti=${nobukti}`;
        }
    },

    onPayment: async (nobukti) => {
        blockUI();
        await fetch(
            `${baseUrl}/transactions/orders/laundry/get-detail?nobukti=${nobukti}`
        )
            .then((response) => {
                if (!response.ok) {
                    unBlockUI();

                    throw new Error(
                        swal.fire(
                            "Terjadi kesalahan",
                            "Saat pengambilan data laundry",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                unBlockUI();

                nobuktiInput.value = nobukti;
                categoryInput.value = response.categorylaundry.name;
                qtyInput.value = response.qty_laundry;

                if (response.tipe_pembayaran) {
                    payment.forEach((item) => {
                        if (item.value == response.tipe_pembayaran) {
                            item.checked = true;
                        }
                    });
                } else {
                    payment.forEach((item, i) => {
                        if (i == 0) {
                            item.checked = true;
                        }
                    });
                }

                priceInput.value = response.harga_laundry
                    ? parseInt(response.harga_laundry)
                    : parseInt(response.categorylaundry.price);

                fnLaundry.init.modals.modalLaundryPayment.show();
            });
    },
};

fnLaundry.init.buttons.btnSeacrh.addEventListener("click", () => {
    fnLaundry.init.tables.tbLaundry.ajax
        .url(
            `${baseUrl}/transactions/orders/laundry/get-all-data?s=${moment(
                fnLaundry.init.litepicker.transDate.getStartDate()
            ).format("YYYY-MM-DD")}&e${moment(
                fnLaundry.init.litepicker.transDate.getEndDate()
            ).format("YYYY-MM-DD")}`
        )
        .load();
});

totalBayarInput.addEventListener("input", () => {
    if (totalBayarInput.value - priceInput.value < 0) {
        totalKembaliInput.value = 0;
    } else {
        totalKembaliInput.value =
            totalBayarInput.value - parseInt(priceInput.value);
    }
});

payment.forEach((item) => {
    item.addEventListener("click", () => {
        if (item.value != "tunai") {
            totalBayarInput.value = parseInt(priceInput.value);
            totalKembaliInput.value =
                totalBayarInput.value - parseInt(priceInput.value);
        } else {
            totalBayarInput.value = "";
        }
    });
});

fnLaundry.init.buttons.btnSavePayment.addEventListener("click", async () => {
    blockUI();

    payment.forEach((item) => {
        if (item.checked) {
            categoryPayment = item.value;
        }
    });

    const results = await onSaveJson(
        `${baseUrl}/transactions/orders/laundry/store-payment`,
        JSON.stringify({
            nobukti: nobuktiInput.value,
            payment: categoryPayment,
            totalBayar: totalBayarInput.value,
            _token: fnLaundry.init.buttons.btnSavePayment.dataset.csrf,
        }),
        "post"
    );

    unBlockUI();

    if (results.data.status) {
        swal.fire("Berhasil", results.data.messsage, "success");

        fnLaundry.init.modals.modalLaundryPayment.hide();
        fnLaundry.init.tables.tbLaundry.ajax
            .url(
                `${baseUrl}/transactions/orders/laundry/get-all-data?s=${startDate.format(
                    "YYYY-MM-DD"
                )}&e${endDate.format("YYYY-MM-DD")}`
            )
            .load();
    } else {
        if (results.data.message.totalBayar) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.totalBayar[0],
                "error"
            );
            return false;
        }

        if (typeof results.data.message == "string") {
            swal.fire("Terjadi kesalahan", results.data.messsage, "error");
            return false;
        }
    }
});
