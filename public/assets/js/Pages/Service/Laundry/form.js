const nobuktiInput = document.querySelector("#nobukti");
const quantityInput = document.querySelector("#quantity");
const categorySelect = document.querySelectorAll(
    'input[name="category-laundry"]'
);
const paymentSelect = document.querySelectorAll('input[name="payment"]');
const subTotalInput = document.querySelector("#sub-total");
const paymentTotalInput = document.querySelector("#total-bayar");
const kembalianInput = document.querySelector("#total-kembali");

let url, data, method;

const fnFormLaundry = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
            btnTakeLaundry: document.querySelector("#btn-take-laundry"),
        },
        dropdowns: {
            noKamarDropdown: new Choices(document.querySelector("#no-kamar")),
        },
        sections: {
            detailSection: document.querySelector("#detail"),
        },
    },

    onLoad: async () => {
        fnFormLaundry.init.dropdowns.noKamarDropdown.enable();
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-room`,
            fnFormLaundry.init.dropdowns.noKamarDropdown,
            "Pilih Kamar",
            noKamar
        );

        if (noKamar) {
            fnFormLaundry.init.dropdowns.noKamarDropdown.disable();
        }

        if (lunas == 1) {
            fnFormLaundry.init.buttons.btnSave.setAttribute("disabled", true);
        }
    },
};

fnFormLaundry.onLoad();

// quantityInput.addEventListener("keyup", () => {
//     categorySelect.forEach((item) => {
//         if (item.checked) {
//             subTotalInput.value =
//                 parseInt(item.dataset.price) *
//                 parseFloat(
//                     parseFloat(
//                         quantityInput.value == "" ? 0 : quantityInput.value
//                     ) / item.dataset.weight
//                 );
//         }
//     });
// });

categorySelect.forEach((item) => {
    item.addEventListener("click", async () => {
        // if (quantityInput.value == "") {
        //     swal.fire("Terjadi kesalahan", "Berat tidak boleh kosong", "error");
        //     return false;
        // }
        quantityInput.value = item.dataset.weight;

        subTotalInput.value = parseInt(item.dataset.price);

        console.log(parseInt(item.dataset.price));

        paymentSelect.forEach((item) => {
            if (item.checked) {
                switch (item.value) {
                    case "transfer":
                    case "qris":
                    case "saldo":
                        paymentTotalInput.value = parseInt(
                            subTotalInput.value.replace(/[^0-9-,]/g, "")
                        );

                        subTotal = parseInt(
                            subTotalInput.value.replace(/[^0-9-,]/g, "")
                        );

                        kembalian =
                            paymentTotalInput.value.replace(/[^0-9-,]/g, "") -
                            subTotal;

                        if (kembalian >= 0) {
                            kembalianInput.value = kembalian;
                        }
                        break;

                    default:
                        paymentTotalInput.value = "";
                        kembalianInput.value = "";
                        break;
                }
            }
        });
    });
});

paymentSelect.forEach((item) => {
    item.addEventListener("click", async () => {
        // if (quantityInput.value == "") {
        //     swal.fire("Terjadi kesalahan", "Berat tidak boleh kosong", "error");
        //     return false;
        // }

        if (item.checked) {
            switch (item.value) {
                case "transfer":
                case "qris":
                case "saldo":
                    paymentTotalInput.value = parseInt(
                        subTotalInput.value.replace(/[^0-9-,]/g, "")
                    );

                    subTotal = parseInt(
                        subTotalInput.value.replace(/[^0-9-,]/g, "")
                    );

                    kembalian =
                        paymentTotalInput.value.replace(/[^0-9-,]/g, "") -
                        subTotal;

                    if (kembalian >= 0) {
                        kembalianInput.value = kembalian;
                    }
                    break;

                default:
                    paymentTotalInput.value = "";
                    kembalianInput.value = "";
                    break;
            }
        }
    });
});

fnFormLaundry.init.buttons.btnSave.addEventListener("click", async () => {
    let kategori = Array.from(categorySelect).find((item) => item.checked);

    let payment = Array.from(paymentSelect).find((item) => item.checked);

    url = `${baseUrl}/transactions/orders/laundry`;

    method = "post";

    data = JSON.stringify({
        nobukti: nobuktiInput.value,
        noKamar: fnFormLaundry.init.dropdowns.noKamarDropdown.getValue(true),
        kategori: kategori ? kategori.value : "",
        berat: quantityInput.value,
        payment: payment.value,
        totalPayment: paymentTotalInput.value,
        kembalian: kembalianInput.value,
        _token: fnFormLaundry.init.buttons.btnSave.dataset.csrf,
    });

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

        window.location.href = `${baseUrl}/transactions/orders`;
    } else {
        if (results.data.message.noKamar) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.noKamar[0],
                "error"
            );
            return false;
        }

        if (results.data.message.kategori) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.kategori[0],
                "error"
            );
            return false;
        }
        swal.fire("Terjadi kesalahan", results.data.message, "error");
    }
});

fnFormLaundry.init.buttons.btnTakeLaundry.addEventListener(
    "click",
    async () => {
        let kategori = Array.from(categorySelect).find((item) => item.checked);

        let payment = Array.from(paymentSelect).find((item) => item.checked);

        url = `${baseUrl}/transactions/orders`;

        method = "post";

        data = JSON.stringify({
            nobukti: nobuktiInput.value,
            noKamar:
                fnFormLaundry.init.dropdowns.noKamarDropdown.getValue(true),
            kategori: kategori.value,
            berat: quantityInput.value,
            payment: payment.value,
            totalPayment: paymentTotalInput.value,
            kembalian: kembalianInput.value,
            _token: fnFormLaundry.init.buttons.btnSave.dataset.csrf,
        });

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

            window.location.href = `${baseUrl}/transactions/orders`;
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
        }
    }
);
