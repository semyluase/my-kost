const inputMember = document.querySelector("#member");
const selectTopUp = document.querySelectorAll("#select-topup");
const inputTopup = document.querySelector("#topup");
const selectPayment = document.querySelectorAll("#select-payment");
const inputSubtotal = document.querySelector("#sub-total");
const inputPayment = document.querySelector("#payment");
const inputKembalian = document.querySelector("#kembalian");

let url, data, method, subTotal, kembalian;

const fnTopup = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
        },
        section: {
            sectionDetail: document.querySelector("#detail-transaction"),
        },
    },

    onLoad: async () => {
        inputMember.value = "";

        selectTopUp.forEach((item, i) => {
            if (i == 0) {
                item.checked = true;
            }
        });

        selectPayment.forEach((item, i) => {
            if (i == 0) {
                item.checked = true;
            }
        });

        inputTopup.value = "";
        inputSubtotal.value = "";
        inputPayment.value = "";
        inputKembalian.value = "";

        await fetch(`${baseUrl}/transactions/orders/top-up/detail`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        swal.fire(
                            "Terjadi kesalahan",
                            "saat pengambilan data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                fnTopup.init.section.sectionDetail.innerHTML = response;
            });
    },
};

fnTopup.onLoad();

selectTopUp.forEach((item) => {
    item.addEventListener("click", () => {
        inputSubtotal.value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(item.value);

        selectPayment.forEach((item) => {
            if (item.checked) {
                switch (item.value) {
                    case "transfer":
                        inputPayment.value = parseInt(
                            inputSubtotal.value.replace(/[^0-9-,]/g, "")
                        );
                        subTotal = parseInt(
                            inputSubtotal.value.replace(/[^0-9-,]/g, "")
                        );
                        kembalian = inputPayment.value - subTotal;

                        if (kembalian >= 0) {
                            inputKembalian.value = new Intl.NumberFormat(
                                "id-ID",
                                {
                                    style: "currency",
                                    currency: "IDR",
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

        inputTopup.value = "";
    });
});

selectPayment.forEach((item) => {
    item.addEventListener("click", () => {
        switch (item.value) {
            case "transfer":
                inputPayment.value = parseInt(
                    inputSubtotal.value.replace(/[^0-9-,]/g, "")
                );
                subTotal = parseInt(
                    inputSubtotal.value.replace(/[^0-9-,]/g, "")
                );
                kembalian = inputPayment.value - subTotal;

                if (kembalian >= 0) {
                    inputKembalian.value = new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
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
inputTopup.addEventListener("keyup", () => {
    inputSubtotal.value = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(inputTopup.value);

    if (inputTopup.value != "" || inputTopup.value > 0) {
        selectTopUp.forEach((item) => {
            if (item.checked) {
                item.checked = false;
            }
        });
    }

    selectPayment.forEach((item) => {
        if (item.checked) {
            switch (item.value) {
                case "transfer":
                    inputPayment.value = parseInt(
                        inputSubtotal.value.replace(/[^0-9-,]/g, "")
                    );
                    subTotal = parseInt(
                        inputSubtotal.value.replace(/[^0-9-,]/g, "")
                    );
                    kembalian = inputPayment.value - subTotal;

                    if (kembalian >= 0) {
                        inputKembalian.value = new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                        }).format(kembalian);
                    }
                    break;

                default:
                    inputPayment.value = "";
                    inputKembalian.value = "";
                    break;
            }
        }
    });
});

inputPayment.addEventListener("keyup", () => {
    if (inputSubtotal.value == "") {
        selectTopUp.forEach((item) => {
            if (item.checked) {
                inputSubtotal.value = new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                }).format(item.value);
            }
        });
    }

    subTotal = parseInt(inputSubtotal.value.replace(/[^0-9-,]/g, ""));

    kembalian = inputPayment.value - subTotal;

    if (kembalian >= 0) {
        inputKembalian.value = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(kembalian);
    }
});

fnTopup.init.buttons.btnSave.addEventListener("click", async () => {
    let topup = 0;
    Array.from(selectTopUp).filter((item) => {
        if (item.checked) {
            topup = item.value;
        }
    });

    if (topup == 0) {
        topup = inputTopup.value;
    }

    let typePayment = "";
    Array.from(selectPayment).forEach((item) => {
        if (item.checked) {
            typePayment = item.value;
        }
    });

    url = `${baseUrl}/transactions/orders/top-up`;

    data = JSON.stringify({
        member: inputMember.value,
        jumlahTopup: topup,
        typePayment: typePayment,
        subTotal: subTotal,
        payment: inputPayment.value,
        kembalian: kembalian,
        _token: fnTopup.init.buttons.btnSave.dataset.csrf,
    });

    method = "post";

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swal.fire("Berhasil", results.data.message, "success").then(
            (result) => {
                if (result.isConfirmed) {
                    fnTopup.onLoad();
                }
            }
        );
    } else {
        swal.fire("Terjadi kesalahan", results.data.message, "error");
    }
});
