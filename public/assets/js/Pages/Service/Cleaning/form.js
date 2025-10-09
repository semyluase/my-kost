const nobuktiInput = document.querySelector("#nobukti");
const jamRequestInput = document.querySelector("#waktu-cleaning");
const selectPayment = document.querySelectorAll("#select-payment");
const inputSubtotal = document.querySelector("#sub-total");
const inputPayment = document.querySelector("#payment");
const inputKembalian = document.querySelector("#kembalian");

let url,
    data,
    method,
    subTotal,
    kembalian,
    typePayment,
    tanggalCleaning = moment();

const fnCleaning = {
    init: {
        buttons: {
            btnSimpan: document.querySelector("#btn-save"),
        },
        dropdowns: {
            kamarDropdown: new Choices(document.querySelector("#no-kamar"), {
                shouldSort: false,
            }),
        },
        datePicker: {
            tanggal: new Litepicker({
                element: document.querySelector("#tanggal"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                format: "DD/MM/YYYY",
                singleMode: true,
                startDate: tanggalCleaning,
                lang: "id-ID",
            }),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-room`,
            fnCleaning.init.dropdowns.kamarDropdown,
            "Pilih Kamar",
            noKamar
        );

        inputSubtotal.value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            trailingZeroDisplay: "stripIfInteger",
        }).format(parseInt(inputSubtotal.value.replace(/[^0-9-,]/g, "")));

        selectPayment.forEach((item) => {
            if (item.checked) {
                switch (item.value) {
                    case "transfer":
                    case "qris":
                    case "saldo":
                        inputPayment.value = new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            trailingZeroDisplay: "stripIfInteger",
                        }).format(
                            parseInt(
                                inputSubtotal.value.replace(/[^0-9-,]/g, "")
                            )
                        );

                        subTotal = parseInt(
                            inputSubtotal.value.replace(/[^0-9-,]/g, "")
                        );

                        kembalian =
                            inputPayment.value.replace(/[^0-9-,]/g, "") -
                            subTotal;

                        if (kembalian >= 0) {
                            inputKembalian.value = new Intl.NumberFormat(
                                "id-ID",
                                {
                                    style: "currency",
                                    currency: "IDR",
                                    trailingZeroDisplay: "stripIfInteger",
                                }
                            ).format(kembalian);
                        }
                        break;

                    default:
                        inputPayment.value = "";
                        inputKembalian.value = "";
                        break;
                }
            }
        });
    },
};

fnCleaning.onLoad();

inputPayment.addEventListener("keyup", (event) => {
    if (event.key == "Backspace" || event.key == "Delete") {
        inputPayment.value = "";
    } else {
        inputPayment.value =
            inputPayment.value == "" || inputPayment.value == "Rp "
                ? ""
                : new Intl.NumberFormat("id-ID", {
                      style: "currency",
                      currency: "IDR",
                      trailingZeroDisplay: "stripIfInteger",
                  }).format(inputPayment.value.replace(/[^0-9-,]/g, ""));

        inputSubtotal.value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            trailingZeroDisplay: "stripIfInteger",
        }).format(parseInt(inputSubtotal.value.replace(/[^0-9-,]/g, "")));

        subTotal = parseInt(inputSubtotal.value.replace(/[^0-9-,]/g, ""));
        kembalian = inputPayment.value.replace(/[^0-9-,]/g, "") - subTotal;

        if (kembalian >= 0) {
            inputKembalian.value = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                trailingZeroDisplay: "stripIfInteger",
            }).format(kembalian);
        }
    }
});

selectPayment.forEach((item) => {
    item.addEventListener("click", () => {
        switch (item.value) {
            case "saldo":
            case "transfer":
            case "qris":
                inputSubtotal.value = new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    trailingZeroDisplay: "stripIfInteger",
                }).format(
                    parseInt(inputSubtotal.value.replace(/[^0-9-,]/g, ""))
                );

                subTotal = parseInt(
                    inputSubtotal.value.replace(/[^0-9-,]/g, "")
                );
                kembalian =
                    inputPayment.value.replace(/[^0-9-,]/g, "") - subTotal;

                if (kembalian >= 0) {
                    inputKembalian.value = new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        trailingZeroDisplay: "stripIfInteger",
                    }).format(kembalian);
                }
                break;

            default:
                inputPayment.value = "";
                inputKembalian.value = "";
                break;
        }
    });
});

fnCleaning.init.buttons.btnSimpan.addEventListener("click", async () => {
    Array.from(selectPayment).forEach((item) => {
        if (item.checked) {
            typePayment = item.value;
        }
    });

    url = `${baseUrl}/transactions/orders/cleaning`;

    method = "post";

    data = JSON.stringify({
        nobukti: nobuktiInput.value,
        noKamar: fnCleaning.init.dropdowns.kamarDropdown.getValue(true),
        jamRequest: jamRequestInput.value,
        typePayment: typePayment,
        totalBayar: inputPayment.value.replace(/[^0-9-,]/g, ""),
        kembalian: inputKembalian.value.replace(/[^0-9-,]/g, ""),
        tanggal: moment(
            fnCleaning.init.datePicker.tanggal.getDate().toJSDate()
        ).format("YYYY-MM-DD"),
        _token: fnCleaning.init.buttons.btnSimpan.dataset.csrf,
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

        if (nobuktiInput.value == "") {
            location.href = `${baseUrl}/transactions/orders`;
        }

        fnCleaning.onLoad();
    } else {
        if (results.data.message.jamRequest) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.jamRequest[0],
                "error"
            );

            return false;
        }

        swal.fire("Terjadi kesalahan", results.data.message, "error");
    }
});
